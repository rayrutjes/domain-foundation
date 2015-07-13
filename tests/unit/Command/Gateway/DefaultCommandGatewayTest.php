<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Command\Gateway;

use RayRutjes\DomainFoundation\Command\Gateway\DefaultCommandGateway;

class DefaultCommandGatewayTest extends \PHPUnit_Framework_TestCase
{
    private $commandBus;

    /**
     * @var DefaultCommandGateway
     */
    private $commandGateway;

    public function setUp()
    {
        $this->commandBus = $this->getMockBuilder('RayRutjes\DomainFoundation\Command\Bus\CommandBus')->getMock();

        $this->commandGateway = new DefaultCommandGateway($this->commandBus);
    }

    public function testImplementsCommandGatewayInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\Gateway\CommandGateway', $this->commandGateway);
    }

    public function testDispatchesACommandThroughTheCommandBus()
    {
        $command = $this->getMockBuilder('RayRutjes\DomainFoundation\Command\Command')->getMock();

        $this->commandBus->expects($this->once())
                         ->method('dispatch')
                         ->with($command);

        $this->commandGateway->send($command);
    }

    public function testTranslatesAPayloadToACommand()
    {
        $payload = $this->getMockBuilder('RayRutjes\DomainFoundation\Serializer\Serializable')->getMock();

        $this->commandBus->expects($this->once())
                         ->method('dispatch')
                         ->with($this->isInstanceOf('RayRutjes\DomainFoundation\Command\Command'));

        $this->commandGateway->send($payload);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsAnExceptionIfThePayloadCannotBeTranslatedIntoACommand()
    {
        $payload = new \stdClass();

        $this->commandGateway->send($payload);
    }

    public function testDispatchesACommandThroughTheCommandBusAndWaitForTheResult()
    {
        $command = $this->getMockBuilder('RayRutjes\DomainFoundation\Command\Command')->getMock();

        $this->commandBus->expects($this->once())
            ->method('dispatch')
            ->with($command);

        $this->commandGateway->sendAndWait($command);
    }
}
