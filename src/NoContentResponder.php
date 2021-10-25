<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;


class NoContentResponder implements ResponderInterface
{

    use ResponseFactoryTrait;


    /**
     * @param ResponseFactoryInterface|null $response_factory Optional: PSR-17 Response Factory
     */
    public function __construct(ResponseFactoryInterface $response_factory = null )
    {
        if ($response_factory) {
            $this->setResponseFactory($response_factory);
        }
    }



    /**
     * @inheritDoc
     */
    public function __invoke( $thingy, int $status = 204 ) : ResponseInterface
    {
        return $this->createResponse( $thingy, $status);
    }


    /**
     * @inheritDoc
     */
    public function createResponse( $thingy, int $status = 204) : ResponseInterface
    {
        return $this->getResponseFactory()->createResponse()
                                          ->withStatus(204);
    }


}
