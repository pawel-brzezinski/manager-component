<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Before add cache tag event for ORM managers.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
class AfterFetchDataEvent extends Event
{
    const NAME = 'ft.manager.after.fetch.data';

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $cacheKey;

    /**
     * AfterFetchDataEvent constructor.
     *
     * @param mixed $data
     * @param null|string $cacheKey
     */
    public function __construct($data, ?string $cacheKey = null)
    {
        $this->data = $data;
        $this->cacheKey = $cacheKey;
    }

    /**
     * Get data.
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get cache key.
     *
     * @return null|string
     */
    public function getCacheKey(): ?string
    {
        return $this->cacheKey;
    }
}
