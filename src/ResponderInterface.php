<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{


    /**
     * Creates a response from the given data.
     *
     * @param  mxied $result
     * @return ResponseInterface
     *
     * @throws ResponderExceptionInterface
     */
    public function createResponse( $result ) : ResponseInterface;
}
