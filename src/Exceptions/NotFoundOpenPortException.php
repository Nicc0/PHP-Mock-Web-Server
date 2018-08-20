<?php

namespace Nicc0\MockWebServer\Exceptions;

/**
 * Class NotFoundOpenPortException
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Exceptions
 */
class NotFoundOpenPortException extends MockWebServerException
{
    /**
     * NotFindOpenPortException constructor.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Failed to find open port', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}