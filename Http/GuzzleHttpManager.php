<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Http;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SmartInt\Component\Cache\ClientInterface as CacheClientInterface;
use SmartInt\Component\Manager\AbstractManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Guzzle HTTP manager.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
class GuzzleHttpManager extends AbstractManager implements GuzzleHttpManagerInterface
{
    /**
     * @var array
     */
    protected $clientConfig = [];

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ORMManager constructor.
     *
     * @param array $clientConfig                               Standard Guzzle Client options.
     * @param CacheClientInterface $cacheClient
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        array $clientConfig = [],
        CacheClientInterface $cacheClient,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->clientConfig = $clientConfig;
        $this->logger = $logger;

        $this
            ->setCacheClient($cacheClient)
            ->setEventDispatcher($eventDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    public function createClient(): Client
    {
        return new Client($this->clientConfig);
    }
}
