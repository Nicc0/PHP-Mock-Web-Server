<?php

namespace Nicc0\MockWebServer;

/**
 * Class ResponseConst
 *
 * @since 15.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer
 */
class ResponseConst
{
    /** @var string */
    public const METHOD_GET     = 'GET';

    /** @var string */
    public const METHOD_POST    = 'POST';

    /** @var string */
    public const METHOD_PUT     = 'PUT';

    /** @var string */
    public const METHOD_PATCH   = 'PATCH';

    /** @var string */
    public const METHOD_DELETE  = 'DELETE';

    /** @var string */
    public const METHOD_HEAD    = 'HEAD';

    /** @var string */
    public const METHOD_OPTIONS = 'OPTIONS';

    /** @var string */
    public const METHOD_TRACE   = 'TRACE';

    /**
     * @param string $method
     * @return string
     */
    public static function getMethod(string $method): ?string
    {
        try {
            $clazz = new \ReflectionClass(__CLASS__);

            if (\in_array(strtoupper($method), $clazz->getConstants(), true)) {
                return strtoupper($method);
            }

            throw new \RuntimeException('Request method not found');
        } catch (\Throwable $ex) {
            return null;
        }
    }
}