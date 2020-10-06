<?php
namespace tests;

use Germania\Responder\TwigResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment as TwigEnvironment;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Argument;


class TwigResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;



    public function testInstantiation()
    {
        $twig_stub = $this->prophesize(TwigEnvironment::class);
        $twig = $twig_stub->reveal();

        $sut = new TwigResponder($twig, "template");
        $this->assertInstanceOf(ResponderInterface::class, $sut);

        return $sut;
    }




    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $sut )
    {
        // Setup response
        $result_response_mock = $this->prophesize(ResponseInterface::class);
        $result_response = $result_response_mock->reveal();

        $render_result = "<html>";

        $twig_stub = $this->prophesize(TwigEnvironment::class);
        $twig_stub->render(Argument::type('string'), Argument::type('array'))->willReturn($render_result);
        $twig = $twig_stub->reveal();

        $sut->setTwig( $twig );
        $sut->setDefaultContext( ['template' => 'tpl'] );

        $response = $sut->createResponse( $thingy );

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $response_body = (string) $response->getBody();
        $this->assertEquals($response_body, $render_result);
    }

    public function provideJsonSerializableData()
    {
        return array(
            [ array("foo" => "bar") ],
            [ new \ArrayObject(array("foo" => "bar")) ]
        );
    }





    /**
     * @dataProvider provideInvalidData
     * @depends testInstantiation
     */
    public function testExceptions($thingy, $sut )
    {
        $this->expectException(ResponderExceptionInterface::class);
        $this->expectException(ResponderInvalidArgumentException::class);
        $sut->createResponse( $thingy );
    }

    public function provideInvalidData()
    {
        return array(
            [ tmpfile() ],
            [ new \StdClass ],
        );
    }



}
