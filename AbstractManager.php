<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager;

use Psr\Log\LoggerInterface;
use SmartInt\Component\Cache\ClientInterface;
use SmartInt\Component\Cache\Resolver\CacheResolverInterface;
use SmartInt\Component\Manager\Exception\MethodNotExistsException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract for manager class.
 *
 * @abstract
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
abstract class AbstractManager implements ManagerInterface
{
    /**
     * @var bool
     */
    protected $cacheEnabled = false;

    /**
     * @var string
     */
    protected $cacheKeyPrefix = '';

    /**
     * @var ClientInterface
     */
    protected $cacheClient;

    /**
     * @var CacheResolverInterface
     */
    protected $cacheResolver;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * {@inheritdoc}
     */
    public function getCacheClient()
    {
        return $this->cacheClient;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheClient(ClientInterface $cacheClient)
    {
        $this->cacheClient = $cacheClient;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheResolver(): ?CacheResolverInterface
    {
        return $this->cacheResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheResolver(CacheResolverInterface $cacheResolver)
    {
        $this->cacheResolver = $cacheResolver;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function enableCache()
    {
        $this->cacheEnabled = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function disableCache()
    {
        $this->cacheEnabled = false;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled && null !== $this->cacheClient;
    }

    /**
     * {@inheritdoc}
     */
    public function buildCacheKey(string $key): string
    {
        return $this->cacheKeyPrefix ? $this->cacheKeyPrefix . '.' . $key : $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * {@inheritdoc}
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

        if (!$cacheKey || !$this->isCacheEnabled()) {
            return $this->$method(...$params);
        }

        $cacheKey = $this->buildCacheKey($cacheKey);

        if ($this->cacheClient->has($cacheKey)) {
            return $this->cacheClient->get($cacheKey);
        }

        $value = $this->$method(...$params);

        if (null === $value) {
            return null;
        }

        $this->cacheClient->set($cacheKey, $value, $cacheLifetime);

        return $value;
    }
}
