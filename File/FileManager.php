<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\File;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use SmartInt\Component\Cache\ClientInterface as CacheClientInterface;
use SmartInt\Component\Manager\AbstractManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * File manager.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
class FileManager extends AbstractManager implements FileManagerInterface
{
    /**
     * FeedManager constructor.
     *
     * @param CacheClientInterface $cacheClient
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        CacheClientInterface $cacheClient,
        EventDispatcherInterface $eventDispatcher = null,
        LoggerInterface $logger = null
    ) {
        $this->setCacheClient($cacheClient);

        if (null !== $eventDispatcher) {
            $this->setEventDispatcher($eventDispatcher);
        }

        if (null !== $logger) {
            $this->setLogger($logger);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHttpClient(array $headers = [], string $username = null, string $password = null)
    {
        return new Client([
            'headers' => $headers,
            'auth' => [$username, $password],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function loadFileContentFromHttp($url, $headers = [], $username = null, $password = null)
    {
        $client = new Client();

        try {
            return $client->request('GET', $url, [
                'headers' => $headers,
                'auth' => [$username, $password],
            ]);
        } catch (\Exception $e) {
            return null;
        }
    }
}
