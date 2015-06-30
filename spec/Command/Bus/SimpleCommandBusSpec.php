<?php

namespace spec\RayRutjes\DomainFoundation\Command\Bus;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Command\Callback\CommandCallback;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;
use RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class SimpleCommandBusSpec extends ObjectBehavior
{
    public function let(CommandHandlerRegistry $handlerRegistry, UnitOfWork $unitOfWork)
    {
        $this->beConstructedWith($handlerRegistry, $unitOfWork);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Bus\SimpleCommandBus');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Bus\CommandBus');
    }

    public function it_can_dispatch_a_command(Command $command, $handlerRegistry, CommandHandler $handler, $unitOfWork)
    {
        $handlerRegistry->findCommandHandlerFor($command)->willReturn($handler);
        $handler->handle($command)->shouldBeCalled();

        $unitOfWork->start()->shouldBeCalled();
        $unitOfWork->commit()->shouldBeCalled();

        $this->dispatch($command);
    }

    public function it_should_rollback_the_unit_of_work_when_an_exception_is_raised_during_command_handling(Command $command, CommandCallback $callback, $handlerRegistry, CommandHandler $handler, $unitOfWork)
    {
        $handlerRegistry->findCommandHandlerFor($command)->willReturn($handler);

        $failureCause = new \Exception();
        $handler->handle($command)->willThrow($failureCause);

        $unitOfWork->start()->shouldBeCalled();
        $unitOfWork->rollback($failureCause)->shouldBeCalledTimes(1);

        $this->shouldThrow($failureCause)->during('dispatch', [$command]);
    }

    public function it_triggers_the_callback_on_success_when_no_exception_is_raised_during_command_handling(Command $command, CommandCallback $callback, $handlerRegistry, CommandHandler $handler)
    {
        $handlerRegistry->findCommandHandlerFor($command)->willReturn($handler);
        $handler->handle($command)->willReturn('result');

        $callback->onSuccess('result')->shouldBeCalledTimes(1);
        $callback->onFailure(Argument::any())->shouldNotBeCalled();

        $this->dispatch($command, $callback);
    }

    public function it_triggers_the_callback_on_failure_when_an_exception_is_raised_during_command_handling(Command $command, CommandCallback $callback, $handlerRegistry, CommandHandler $handler)
    {
        $handlerRegistry->findCommandHandlerFor($command)->willReturn($handler);

        $failureCause = new \Exception();
        $handler->handle($command)->willThrow($failureCause);

        $callback->onFailure($failureCause)->shouldBeCalledTimes(1);
        $callback->onSuccess(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow($failureCause)->during('dispatch', [$command, $callback]);
    }
}
