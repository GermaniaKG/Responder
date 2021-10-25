<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;


class JsonResponder implements ResponderInterface
{

    use ResponseFactoryTrait;


    /**
     * @var int
     */
    public $json_options = 0;


    /**
     * Response content type
     * @var string
     */
    public $response_content_type = 'application/json';



    /**
     * @param int                           $json_options JSON enconding options, default: 0
     * @param ResponseFactoryInterface|null $response_factory Optional: PSR-17 Response Factory
     */
    public function __construct(int $json_options = 0, ResponseFactoryInterface $response_factory = null )
    {
        $this->setJsonOptions($json_options);
        if ($response_factory) {
            $this->setResponseFactory($response_factory);
        }
    }


    /**
     * Sets the JSON options.
     *
     * @param int $json_options
     */
    public function setJsonOptions( int $json_options )
    {
        $this->json_options = $json_options;
        return $this;
    }


    /**
     * Sets the response content type.
     *
     * @param string $content_type
     */
    public function setResponseContentType( string $content_type )
    {
        $this->response_content_type = $content_type;
        return $this;
    }




    /**
     * @inheritDoc
     */
    public function __invoke( $thingy, int $status = 500 ) : ResponseInterface
    {
        return $this->createResponse( $thingy, $status);
    }


    /**
     * @inheritDoc
     */
    public function createResponse( $thingy, int $status = 200) : ResponseInterface
    {
        if (is_resource($thingy)) {
            $msg = sprintf("Can't work with resource types.");
            throw new ResponderInvalidArgumentException($msg);
        }

        if (is_object($thingy)
        and !$thingy instanceOf \JsonSerializable) {
            $msg = sprintf("Expected JsonSerializable instance, instead got '%s'.", get_class($thingy));
            throw new ResponderInvalidArgumentException($msg);
        }

        try {
            $json_thingy = json_encode($thingy, $this->json_options);

            $response = $this->getResponseFactory()->createResponse()
                                                   ->withHeader('Content-type', $this->response_content_type)
                                                   ->withStatus($status);

            $response->getBody()->write($json_thingy);
        }
        catch (\Throwable $e) {
            throw new ResponderRuntimeException("Caught exception during response creation", 1, $e);
        }

        return $response;
    }


}
