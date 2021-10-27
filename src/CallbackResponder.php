<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;

class CallbackResponder extends ResponderDecoratorAbstract implements ResponderInterface
{


    /**
     * @param callable $callback
     * @param ResponderInterface $responder Inner ResponderInterface
     */
    public function __construct( callable $callback, ResponderInterface $responder )
    {
        $this->setCallback($callback);
        parent::__construct( $responder );
    }

    public function setCallback(callable $callback) : self 
    {
        $this->callback = $callback;
        return $this;
    }


    public function getCallback() : ?callable
    {
        return $this->callback;
    }


    /**
     * @inheritDoc
     */
    public function __invoke( $thingy, int $status = 200 ) : ResponseInterface
    {
        return $this->createResponse( $thingy, $status);
    }


    /**
     * @inheritDoc
     */
    public function createResponse( $thingy, int $status = 200 ) : ResponseInterface
    {
        $callback = $this->getCallback() ?: function($thingy) { return $thingy; };
        $mangled_thingy = $callback($thingy);

        return $this->responder->createResponse( $mangled_thingy, $status);
    }
}
