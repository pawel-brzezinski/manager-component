<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\File;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use SmartInt\Component\Manager\ManagerInterface;

/**
 * Interface for file manager implementation.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
interface FileManagerInterface extends ManagerInterface
{
    /**
     * Get HTTP client instance.
     *
     * @param array $headers
     * @param string|null $username
     * @param string|null $password
     *
     * @return Client
     */
    public function getHttpClient(array $headers = [], string $username = null, string $password = null);

    /**
     * Load file content by http client.
     *
     * @param string $url
     * @param array $headers
     * @param null|string $username
     * @param null|string $password
     *
     * @return null|Response
     */
    public function loadFileContentFromHttp($url, $headers = [], $username = null, $password = null);
}
