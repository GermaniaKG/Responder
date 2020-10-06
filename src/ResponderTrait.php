<?php
namespace Germania\Responder;

use Slim\Psr7\Factory\ResponseFactory;

trait ResponderTrait
{


    /**
     * @var ResponderInterface
     */
    protected $responder;


    /**
     * Returns the Responder.
     *
     * @return ResponderInterface|null
     */
    public function getResponder() : ?ResponderInterface
    {
        return $this->responder;
    }


    /**
     * Sets the Responder.
     *
     * @param ResponderInterface $responder
     */
    public function setResponder( ResponderInterface $responder )
    {
        $this->responder = $responder;
        return $this;
    }
}
