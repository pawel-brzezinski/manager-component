<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager;

use Psr\Log\LoggerInterface;
use SmartInt\Component\Cache\ClientInterface;
use SmartInt\Component\Cache\Resolver\CacheResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface for manager implementation.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
interface ManagerInterface
{
    /**
     * Get cache client.
     *
     * @return null|ClientInterface
     */
    public function getCacheClient();

    /**
     * Set cache client.
     *
     * @param ClientInterface $cacheClient
     *
     * @return ManagerInterface
     */
    public function setCacheClient(ClientInterface $cacheClient);

    /**
     * Get cache resolver.
     *
     * @return CacheResolverInterface|null
     */
    public function getCacheResolver(): ?CacheResolverInterface;

    /**
     * Set cache resolver.
     *
     * @param CacheResolverInterface $cacheResolver
     *
     * @return ManagerInterface
     */
    public function setCacheResolver(CacheResolverInterface $cacheResolver);

    /**
     * Enable cache.
     *
     * @return ManagerInterface
     */
    public function enableCache();

    /**
     * Disable cache.
     *
     * @return ManagerInterface
     */
    public function disableCache();

    /**
     * Return flag which determine whether cache is enabled.
     *
     * @return bool
     */
    public function isCacheEnabled();

    /**
     * Build cache key.
     *
     * @param string $key
     *
     * @return string
     */
    public function buildCacheKey(string $key) :string;

    /**
     * Get event dispatcher.
     *
     * @return null|EventDispatcherInterface
     */
    public function getEventDispatcher(): ?EventDispatcherInterface;

    /**
     * Set event dispatcher.
     *
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return ManagerInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher);

    /**
     * Get logger.
     *
     * @return null|LoggerInterface
     */
    public function getLogger(): ?LoggerInterface;

    /**
     * Set logger.
     *
     * @param LoggerInterface $logger
     *
     * @return ManagerInterface
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @param string $method
     * @param array $params
     * @param null|string $cacheKey
     * @param array $cacheTags
     * @param null|int $cacheLifetime
     *
     * @return mixed
     */
    public function fetchData(
        string $method,
        array $params = [],
        ?string $cacheKey = null,
        array $cacheTags = [],
        ?int $cacheLifetime = null
    );
}
