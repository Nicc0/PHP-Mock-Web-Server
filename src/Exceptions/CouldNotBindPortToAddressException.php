<?php

namespace Nicc0\MockWebServer\Exceptions;

/**
 * Class CouldNotBindPortToAddressException
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Exceptions
 */
class CouldNotBindPortToAddressException extends MockWebServerException
{
    /**
     * NotFindOpenPortException constructor.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message = 'Could not bind to address', int $code = 500)
    {
        parent::__construct($message, $code);
    }
}