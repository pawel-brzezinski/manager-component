<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Http;

use GuzzleHttp\ClientInterface;
use SmartInt\Component\Manager\ManagerInterface;

/**
 * Interface for Guzzle HTTP manager implementation.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
interface GuzzleHttpManagerInterface extends ManagerInterface
{
    /**
     * Create Guzzle client instance.
     *
     * @return ClientInterface
     */
    public function createClient();
}
