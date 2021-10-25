<?php
namespace Germania\Responder;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;


class RouteNameRedirectResponder implements ResponderInterface
{

    use ResponseFactoryTrait;

    public $route_name;
    public $uri_creator;
    public $default_status = 301;


    /**
     * @param string $route_name
     * @param callable $uri_creator,
     * @param ResponseFactoryInterface|null $response_factory Optional: PSR-17 Response Factory
     */
    public function __construct(string $route_name, callable $uri_creator, ResponseFactoryInterface $response_factory = null )
    {
        $this->setRouteName($route_name);
        $this->setUriCreator($uri_creator);
        if ($response_factory) {
            $this->setResponseFactory($response_factory);
        }
    }


    public function setUriCreator(callable $uri_creator) : self
    {
        $this->uri_creator = $uri_creator;
        return $this;
    }
    public function getUriCreator() : callable
    {
        return $this->uri_creator;
    }


    public function setRouteName(string $route_name) : self
    {
        $this->route_name = $route_name;
        return $this;
    }
    public function getRouteName() : string
    {
        return $this->route_name;
    }



    /**
     * @inheritDoc
     */
    public function __invoke( $data, int $status = null ) : ResponseInterface
    {
        return $this->createResponse( $data, $status);
    }


    /**
     * @inheritDoc
     */
    public function createResponse( $data, int $status = null) : ResponseInterface
    {
        if ($data instanceOf \JsonSerializable):
            $data = $data->jsonSerialize();
        elseif (!is_array($data)):
            throw new \InvalidArgumentException("Array or JsonSerializable expected");
        endif;

        $redirect_url = ($this->uri_creator)($this->route_name, $data);
        $status = $status ?: $this->default_status;

        return $this->getResponseFactory()->createResponse()
                                          ->withStatus($status)
                                          ->withHeader('Location', $redirect_url);
    }


}
