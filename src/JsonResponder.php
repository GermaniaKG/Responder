<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Factory\ResponseFactory;

class JsonResponder implements ResponderInterface
{

    use ResponseFactoryTrait;


    /**
     * @var int
     */
    public $json_options = \JSON_PRETTY_PRINT;


    /**
     * Response content type
     * @var string
     */
    public $response_content_type = 'application/json';



    /**
     * @param int                      $json_options
     * @param ResponseFactoryInterface $response_factory
     */
    public function __construct(int $json_options = null, ResponseFactoryInterface $response_factory = null )
    {
        $this->setResponseFactory($response_factory ?: new ResponseFactory);

        if (!is_null($json_options)) {
            $this->setJsonOptions($json_options);
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
    public function createResponse( $thingy) : ResponseInterface
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

        $json_thingy = json_encode($thingy, $this->json_options);

        $response = $this->getResponseFactory()->createResponse()
                                               ->withHeader('Content-type', $this->response_content_type)
                                               ->withStatus(200);

        $response->getBody()->write($json_thingy);

        return $response;
    }


}
