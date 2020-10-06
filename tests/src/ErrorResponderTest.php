<?php
namespace tests;

use Germania\Responder\ErrorResponder;
use Germania\Responder\JsonResponder;
use Germania\Responder\ResponderInterface;
use Germania\Responder\ResponderInvalidArgumentException;
use Germania\Responder\ResponderExceptionInterface;
use Psr\Http\Message\ResponseInterface;

use Prophecy\PhpUnit\ProphecyTrait;


class ErrorResponderTest extends \PHPUnit\Framework\TestCase
{
    use ProphecyTrait;

    public $inner_responder;

    public function setUp() : void
    {
        parent::setUp();

        $this->inner_responder = new JsonResponder();

    }

    public function testInstantiation()
    {
        $debug = false;
        $sut = new ErrorResponder($debug, $this->inner_responder);
        $this->assertInstanceOf(ResponderInterface::class, $sut);

        return $sut;
    }


    /**
     * @dataProvider provideJsonSerializableData
     * @depends testInstantiation
     */
    public function testResponseCreation($thingy, $debug, $sut )
    {
        $sut->setDebug($debug);
        $response = $sut->createResponse( $thingy );

        $this->assertInstanceOf(ResponseInterface::class, $response);

        $response_body = (string) $response->getBody();
        $response_body_decoded = json_decode($response_body, "force_array");

        $this->assertIsArray($response_body_decoded['errors']);
    }


    public function provideJsonSerializableData()
    {

        $E1 = new \RuntimeException("Boo!");
        return array(
            [ $E1, true],
            [ new \Exception("Outer", 0, $E1), true ],
            [ $E1, false],
            [ new \Exception("Outer", 0, $E1), false ],
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
            [ "foo" ]
        );
    }




}
