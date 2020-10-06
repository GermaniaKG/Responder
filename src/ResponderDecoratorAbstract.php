<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;

abstract class ResponderDecoratorAbstract implements ResponderInterface
{

    use ResponderTrait;


    /**
     * @param ResponderInterface $responder Inner ResponderInterface
     */
    public function __construct( ResponderInterface $responder )
    {
        $this->setResponder($responder);
    }


    /**
     * @inheritDoc
     */
    abstract public function createResponse( $result ) : ResponseInterface;
}
