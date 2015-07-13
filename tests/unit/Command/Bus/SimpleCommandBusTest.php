<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Command\Bus;

use RayRutjes\DomainFoundation\Command\Bus\SimpleCommandBus;

class SimpleCommandBusTest extends \PHPUnit_Framework_TestCase
{
    private $handlerRegistry;

    private $unitOfWork;

    /**
     * @var SimpleCommandBus
     */
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

    public function testCanInterceptCommandBeforeDispatching()
    {
        $command = $this->commandMock();

        $this->handlerRegistry->method('findCommandHandlerFor')
            ->willReturn($this->commandHandlerMock());

        $dispatchInterceptor1 = $this->commandDispatchInterceptorMock();
        $dispatchInterceptor1->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($command))
            ->will($this->returnArgument(0));

        $dispatchInterceptor2 = $this->commandDispatchInterceptorMock();
        $dispatchInterceptor2->expects($this->once())
            ->method('handle')
            ->with($this->identicalTo($command))
            ->will($this->returnArgument(0));

        $this->commandBus->addDispatchInterceptor($dispatchInterceptor1, $dispatchInterceptor2);

        $this->commandBus->dispatch($command);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresCorrectTypeOfAddedDispatchInterceptors()
    {
        $this->commandBus->addDispatchInterceptor(new \stdClass());
    }

    public function testCanInterceptCommandBeforeItIsBeingHandled()
    {
        $command = $this->commandMock();

        $this->handlerRegistry->method('findCommandHandlerFor')
            ->willReturn($this->commandHandlerMock());

        $handlerInterceptor1 = $this->commandHandlerInterceptorMock();
        $handlerInterceptor1->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($command),
                $this->identicalTo($this->unitOfWork),
                $this->isInstanceOf('RayRutjes\DomainFoundation\Command\Interceptor\InterceptorChain')
            )->will($this->returnCallback(function ($command, $unitOfWork, $interceptorChain) {
                return $interceptorChain->proceed();
            }));

        $handlerInterceptor2 = $this->commandHandlerInterceptorMock();
        $handlerInterceptor2->expects($this->once())
            ->method('handle')
            ->with(
                $this->identicalTo($command),
                $this->identicalTo($this->unitOfWork),
                $this->isInstanceOf('RayRutjes\DomainFoundation\Command\Interceptor\InterceptorChain')
            );

        $this->commandBus->addHandlerInterceptor($handlerInterceptor1, $handlerInterceptor2);

        $this->commandBus->dispatch($command);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresCorrectTypeOfAddedHandlerInterceptors()
    {
        $this->commandBus->addHandlerInterceptor(new \stdClass());
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandDispatchInterceptorMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Interceptor\CommandDispatchInterceptor');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandHandlerInterceptorMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Interceptor\CommandHandlerInterceptor');
    }
}
