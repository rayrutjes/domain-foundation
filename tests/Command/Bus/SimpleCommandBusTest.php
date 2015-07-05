<?php

namespace RayRutjes\DomainFoundation\Test\Command\Bus;

use RayRutjes\DomainFoundation\Command\Bus\SimpleCommandBus;

class SimpleCommandBusTest extends \PHPUnit_Framework_TestCase
{
    private $handlerRegistry;

    private $unitOfWork;

    private $commandBus;

    public function setUp()
    {
        $this->handlerRegistry = $this->commandHandlerRegistryMock();
        $this->unitOfWork = $this->unitOfWorkMock();

        $this->commandBus = new SimpleCommandBus($this->handlerRegistry, $this->unitOfWork);
    }

    public function testImplementsCommandBusInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\Bus\CommandBus', $this->commandBus);
    }

    public function testDelegatesHandlingOfDispatchedCommand()
    {
        $command = $this->commandMock();

        $commandHandler = $this->commandHandlerMock();
        $commandHandler->expects($this->once())
                       ->method('handle')
                       ->with($this->identicalTo($command));

        $this->handlerRegistry->method('findCommandHandlerFor')
                              ->with($this->identicalTo($command))
                              ->willReturn($commandHandler);

        $this->unitOfWork->expects($this->once())
                         ->method('start');

        $this->unitOfWork->expects($this->once())
                         ->method('commit');

        $this->commandBus->dispatch($command);
    }

    public function testTriggersCallbackOnDispatchSuccess()
    {
        $command = $this->commandMock();

        $callback = $this->commandCallbackMock();
        $callback->expects($this->once())
                 ->method('onSuccess')
                 ->with('result');

        $callback->expects($this->never())
                 ->method('onFailure');

        $commandHandler = $this->commandHandlerMock();
        $commandHandler->method('handle')
                       ->willReturn('result');

        $this->handlerRegistry->method('findCommandHandlerFor')
                              ->willReturn($commandHandler);

        $this->commandBus->dispatch($command, $callback);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTriggersCallbackOnDispatchFailure()
    {
        $command = $this->commandMock();

        $callback = $this->commandCallbackMock();

        $callback->expects($this->once())
                 ->method('onFailure')
                 ->with(new \RuntimeException());

        $callback->expects($this->never())
                 ->method('onSuccess');

        $commandHandler = $this->commandHandlerMock();
        $commandHandler->method('handle')
                       ->will($this->throwException(new \RuntimeException()));

        $this->handlerRegistry->method('findCommandHandlerFor')
                              ->willReturn($commandHandler);

        $this->commandBus->dispatch($command, $callback);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRollsBackTheUnitOfWorkOnDispatchFailure()
    {
        $command = $this->commandMock();

        $callback = $this->commandCallbackMock();

        $this->unitOfWork->expects($this->once())
                         ->method('rollback')
                         ->with(new \RuntimeException());

        $commandHandler = $this->commandHandlerMock();
        $commandHandler->method('handle')
                       ->will($this->throwException(new \RuntimeException()));

        $this->handlerRegistry->method('findCommandHandlerFor')
                              ->willReturn($commandHandler);

        $this->commandBus->dispatch($command, $callback);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandHandlerRegistryMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Command');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandHandlerMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Handler\CommandHandler');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandCallbackMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Callback\CommandCallback');
    }
}
