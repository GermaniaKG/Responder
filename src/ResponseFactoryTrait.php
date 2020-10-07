<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseFactoryInterface;

trait ResponseFactoryTrait
{


    /**
     * @var ResponseFactory
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
     * Returns the ResponseFactory or null.
     *
     * @return ResponseFactoryInterface|null
     */
    public function getResponseFactory() : ?ResponseFactoryInterface
    {
        return $this->response_factory;
    }

}
