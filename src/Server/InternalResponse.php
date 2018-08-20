<?php

namespace Nicc0\MockWebServer\Server;

use Nicc0\MockWebServer\Response;

/**
 * Class InternalResponse
 *
 * @internal
 * @since 20.08.2018
 * @author Daniel Tęcza
 * @package Nicc0\MockWebServer\Server
 */
class InternalResponse extends Response
{
    /** @var \Nicc0\MockWebServer\Response */
    private $response;

    /**
     * InternalResponse constructor.
     *
     * @param \Nicc0\MockWebServer\Response $response
     */
    public function __construct(Response $response)
    {
        parent::__construct($response->getOptions());

        $this->response = clone $response;
        $this->response->time = \microtime(true);
    }

    /**
     * @return \Nicc0\MockWebServer\Response
     */
    public function getAsCommonResponse(): Response
    {
        return $this->response;
    }
}