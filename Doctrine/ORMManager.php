<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Doctrine;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use SmartInt\Component\Cache\CacheableInterface;
use SmartInt\Component\Cache\ClientInterface as CacheClientInterface;
use SmartInt\Component\Cache\Doctrine\DoctrineProviderClient;
use SmartInt\Component\Cache\Model\Config;
use SmartInt\Component\Cache\Resolver\CacheResolverInterface;
use SmartInt\Component\Manager\AbstractManager;
use SmartInt\Component\Manager\Event\AfterFetchDataEvent;
use SmartInt\Component\Manager\Exception\MethodNotExistsException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Doctrine ORM manager.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
class ORMManager extends AbstractManager implements ORMManagerInterface
{
    /**
     * @var string
     */
    protected $entityNamespace;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ORMManager constructor.
     *
     * @param string $entityNamespace
     * @param EntityManagerInterface $entityManager
     * @param CacheClientInterface $cacheClient
     * @param CacheResolverInterface $cacheResolver
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @throws \Exception
     */
    public function __construct(
        string $entityNamespace,
        EntityManagerInterface $entityManager,
        CacheClientInterface $cacheClient,
        CacheResolverInterface $cacheResolver = null,
        EventDispatcherInterface $eventDispatcher = null
    ) {
        $this
            ->setEntityNamespace($entityNamespace)
            ->setEntityManager($entityManager)
            ->setCacheClient($cacheClient);

        if (null !== $cacheResolver) {
            $this->setCacheResolver($cacheResolver);
        }

        if (null !== $eventDispatcher) {
            $this->setEventDispatcher($eventDispatcher);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityNamespace(): ?string
    {
        return $this->entityNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityNamespace(string $entityNamespace): self
    {
        if (!class_exists($entityNamespace)) {
            throw new \Exception(sprintf('Class for entity namespace "%s" does not exist.', $entityNamespace));
        }

        $this->entityNamespace = $entityNamespace;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager(): ?EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): ObjectRepository
    {
        $repository = $this->getEntityManager()->getRepository($this->entityNamespace);

        // If repository is an implementation of CacheableInterface then reset cache config object.
        if ($repository instanceof CacheableInterface) {
            $repository->setCacheConfig(new Config());
        }

        return $repository;
    }

    /**
     * {@inheritdoc}
     *
     * This method has been overloaded to force use Doctrine cache for repositories.
     */
    public function fetchData(
        string $method,
        array $params = [],
        string $cacheKey = null,
        array $cacheTags = [],
        ?int $cacheLifetime = null
    ) {
        if (!method_exists($this, $method)) {
            throw new MethodNotExistsException($method);
        }

        if ($this->cacheClient instanceof DoctrineProviderClient) {
            return $this->fetchDataWithDoctrineProviderClientCache($method, $params, $cacheKey, $cacheTags, $cacheLifetime);
        }

        return $this->fetchDataWithNonDoctrineProviderClientCache($method, $params, $cacheKey, $cacheTags, $cacheLifetime);
    }

    /**
     * Fetch data with usage of Doctrine Provider cache client.
     *
     * @param string $method
     * @param array $params
     * @param string|null $cacheKey
     * @param array $cacheTags
     * @param int|null $cacheLifetime
     *
     * @return mixed
     */
    public function fetchDataWithDoctrineProviderClientCache(
        string $method,
        array $params = [],
        string $cacheKey = null,
        array $cacheTags = [],
        ?int $cacheLifetime = null
    ) {
        $repository = $this->getRepository();
        // Inject repository instance.
        array_unshift($params, $repository);

        if (!$cacheKey || !$this->isCacheEnabled() || !$repository instanceof CacheableInterface) {
            return $this->$method(...$params);
        }

        $cacheKey = $this->buildCacheKey($cacheKey);
        $cacheLifetime = null === $cacheLifetime ? 0 : $cacheLifetime;

        $repository->getCacheConfig()->setKey($cacheKey)->setLifetime($cacheLifetime)->setEnabled(true);

        $result = $this->$method(...$params);

        if (!empty($cacheTags)) {
            $this->cacheClient->addTags($cacheKey, $cacheTags);
        }

        if (null !== $eventDispatcher = $this->getEventDispatcher()) {
            $event = new AfterFetchDataEvent($result, $cacheKey);
            $eventDispatcher->dispatch(AfterFetchDataEvent::NAME, $event);
        }

        return $result;
    }

    /**
     * Fetch data without usage of Doctrine Provider cache client.
     *
     * @param string $method
     * @param array $params
     * @param string|null $cacheKey
     * @param array $cacheTags
     * @param int|null $cacheLifetime
     *
     * @return mixed
     */
    public function fetchDataWithNonDoctrineProviderClientCache(
        string $method,
        array $params = [],
        string $cacheKey = null,
        array $cacheTags = [],
        ?int $cacheLifetime = null
    ) {
        $repository = $this->getRepository();
        // Inject repository instance.
        array_unshift($params, $repository);

        if (!$cacheKey || !$this->isCacheEnabled()) {
            return $this->$method(...$params);
        }

        $cacheKey = $this->buildCacheKey($cacheKey);

        if ($this->cacheClient->has($cacheKey)) {
            return $this->cacheClient->get($cacheKey);
        }

        if (!empty($cacheTags)) {
            $this->cacheClient->addTags($cacheKey, $cacheTags);
        }

        $result = $this->$method(...$params);

        if (null === $result) {
            return null;
        }

        if (null !== $eventDispatcher = $this->getEventDispatcher()) {
            $event = new AfterFetchDataEvent($result, $cacheKey);
            $eventDispatcher->dispatch(AfterFetchDataEvent::NAME, $event);
        }

        $this->cacheClient->set($cacheKey, $result, $cacheLifetime);

        return $result;
    }
}
