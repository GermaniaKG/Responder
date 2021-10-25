<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseFactoryInterface;
use Nyholm;

trait ResponseFactoryTrait
{


    /**
     * @var null|ResponseFactory
     */
    public $response_factory;



    /**
     * Sets the ResponseFactory.
     *
     * @param ResponseFactoryInterface $response_factory
     */
    public function setResponseFactory(ResponseFactoryInterface $response_factory )
    {
        $this->response_factory = $response_factory;
        return $this;
    }



    /**
     * Returns the ResponseFactory.
     *
     * If nt set otherwise, the ResponseFactory is `Nyholm\Psr7\Factory\Psr17Factory`
     *
     * @return ResponseFactoryInterface
     */
    public function getResponseFactory() : ResponseFactoryInterface
    {
        if (!$this->response_factory) {
            $this->setResponseFactory( new Nyholm\Psr7\Factory\Psr17Factory );
        }
        return $this->response_factory;
    }

}
