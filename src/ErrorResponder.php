<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;

class ErrorResponder extends ResponderDecoratorAbstract implements ResponderInterface
{

    public $debug = false;


    /**
     * @param bool|boolean       $debug     Turn on debug mode
     * @param ResponderInterface $responder Inner ResponderInterface
     */
    public function __construct( bool $debug = false, ResponderInterface $responder )
    {
        parent::__construct($responder);
        $this->setDebug($debug);
    }


    /**
     * Sets the debug mode.
     * @param bool $debug
     */
    public function setDebug(bool $debug) {
        $this->debug = $debug;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function createResponse( $e, int $status = 500 ) : ResponseInterface
    {
        if (!$e instanceOf \Throwable) {
            $msg = sprintf("Expected Throwable, got '%s'", gettype($e));
            throw new ResponderInvalidArgumentException($msg);
        }

        $exceptions = array($this->throwableToArray($e));
        while ($this->debug and $e = $e->getPrevious()) {
            $exceptions[] = $this->throwableToArray($e);
        }

        $result = array(
            'errors' => $exceptions
        );

        return $this->getResponder()->createResponse($result)
                                    ->withStatus($status);

    }



    protected function throwableToArray (\Throwable $e) : array
    {
        $result = array(
            'type'     => get_class($e),
            'message'  => $e->getMessage(),
            'code'     => $e->getCode()
        );
        if ($this->debug) {
            $result['location'] = sprintf("%s:%s", $e->getFile(), $e->getLine());
        }

        return $result;
    }
}
