<?php
namespace Germania\Responder;

use Twig\Environment as TwigEnvironment;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class TwigResponder implements ResponderInterface
{

    use ResponseFactoryTrait;

    /**
     * @var TwigEnvironment
     */
    public $twig;


    /**
     * @var strinfg
     */
    public $template_field = "template";


    /**
     * @var array
     */
    public $default_context = array();


    /**
     * @var string|bool
     */
    public $default_template = false;



    /**
     * @param TwigEnvironment               $twig             Twig environment
     * @param string                        $template_field   Array field taht will contain template
     * @param array                         $default_context  Default template context
     * @param ResponseFactoryInterface|null $response_factory Optional: PSR-17 Response Factory
     */
    public function __construct( TwigEnvironment $twig, string $template_field, array $default_context = array(), ResponseFactoryInterface $response_factory = null )
    {
        $this->setTwig($twig);
        $this->setTemplateField($template_field);
        $this->setDefaultContext($default_context);
        if ($response_factory) {
            $this->setResponseFactory($response_factory);
        }
    }



    /**
     * @param TwigEnvironment $twig
     */
    public function setTwig(TwigEnvironment $twig )
    {
        $this->twig = $twig;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getDefaultTemplate( )
    {
        return $this->default_template;
    }


    /**
     * @param mixed $default_template
     */
    public function setDefaultTemplate( $default_template )
    {
        $this->default_template = $default_template;
        return $this;
    }



    /**
     * @param array $default_context
     */
    public function setDefaultContext( array $default_context )
    {
        $this->default_context = $default_context;
        return $this;
    }


    /**
     * @param string $field
     */
    public function setTemplateField( string $field )
    {
        $this->template_field = $field;
        return $this;
    }




    /**
     * @inheritDoc
     */
    public function __invoke( $context ) : ResponseInterface
    {
        return $this->createResponse( $context );
    }


    /**
     * @inheritDoc
     * @param array|ArrayObject Context
     *
     * @throws  ResponderInvalidArgumentException
     * @throws  ResponderRuntimeException
     */
    public function createResponse( $context ) : ResponseInterface
    {
        if ($context instanceof \ArrayObject):
            $context = $context->getArrayCopy();
        elseif (!is_array($context)):
            $context_type = is_object($context) ? get_class($context) : gettype($context);
            $msg = sprintf(
                "Expected Array or ArrayObject, got '%s'.",
                $context_type
            );
            throw new ResponderInvalidArgumentException($msg);
        endif;


        // Merge with defaults
        $context = array_merge($this->default_context, $context);
        $template = $context[ $this->template_field ] ?? $this->getDefaultTemplate();

        if (!$template) {
            $msg = sprintf("Expected '%s' element.", $this->template_field);
            throw new ResponderRuntimeException($msg);
        }

        try {
            $website_html = $this->twig->render($template, $context);

            $response = $this->getResponseFactory()->createResponse();
            $response->getBody()->write($website_html);
        }
        catch (\Throwable $e) {
            throw new ResponderRuntimeException("Caught exception during response creation", 1, $e);
        }

        return $response;
    }
}
