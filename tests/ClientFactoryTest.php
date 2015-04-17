<?php
namespace Crunch\FastCGI;

use PHPUnit_Framework_TestCase as TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \Crunch\FastCGI\ClientFactory
 * @covers \Crunch\FastCGI\ClientFactory
 */
class ClientFactoryTest extends TestCase
{
    /** @var ObjectProphecy */
    private $socketFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->socketFactory = $this->prophesize('\Socket\Raw\Factory');
        $this->socketFactory->createClient(Argument::any())->willReturn($this->prophesize('\Socket\Raw\Socket'));
    }

    /**
     * @covers ::connect
     * @uses \Crunch\FastCGI\Client::__construct
     * @uses \Crunch\FastCGI\Connection::__construct
     * @uses \Crunch\FastCGI\Connection::__destruct
     */
    public function testCreateClient()
    {
        $factory = new ClientFactory($this->socketFactory->reveal());


        $connection = $factory->connect('foobar');

        self::assertInstanceOf('\Crunch\FastCGI\Client', $connection);
    }

    /**
     * @covers ::connect
     * @uses \Crunch\FastCGI\Client::__construct
     * @uses \Crunch\FastCGI\Connection::__construct
     * @uses \Crunch\FastCGI\Connection::__destruct
     */
    public function testConnectCanHandleSchemelessUnixSocketPaths()
    {
        $factory = new ClientFactory($this->socketFactory->reveal());
        $factory->connect('/foo/bar');

        $this->socketFactory->createClient(Argument::exact('unix:///foo/bar'))->shouldHaveBeenCalled();
    }
}
