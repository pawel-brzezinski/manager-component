<?php

declare(strict_types=1);

namespace SmartInt\Component\Manager\Exception;

/**
 * Exception is thrown when manager is calling for non existing method.
 *
 * @author Paweł Brzeziński <pawel.b@pawel.brzezinski@smartint.pl.es>
 */
class MethodNotExistsException extends \Exception
{
    /**
     * MethodNotExistsException constructor.
     *
     * @param string $methodName
     */
    public function __construct($methodName)
    {
        $this->message = sprintf('Method "%s" not exists', $methodName);
    }
}