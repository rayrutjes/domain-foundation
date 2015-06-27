<?php

namespace spec\RayRutjes\DomainFoundation\Command\Gateway;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Command\Bus\CommandBus;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Factory\CommandFactory;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultCommandGatewaySpec extends ObjectBehavior
{
    public function let(CommandBus $commandBus, CommandFactory $commandFactory)
    {
        $this->beConstructedWith($commandBus, $commandFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Gateway\DefaultCommandGateway');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Gateway\CommandGateway');
    }

    public function it_dispatches_a_command_through_the_command_bus(Command $command, $commandBus)
    {
        $commandBus->dispatch($command)->shouldBeCalledTimes(1);
        $this->send($command);
    }

    public function it_translates_payloads_to_a_command(Serializable $payload, $commandBus, $commandFactory, Command $command)
    {
        $commandFactory->createFromPayload($payload)->willReturn($command);
        $commandBus->dispatch($command)->shouldBeCalledTimes(1);
        $this->send($payload);
    }

    public function it_should_raise_an_exception_if_sent_data_is_not_supported()
    {
        $this->shouldThrow(new \InvalidArgumentException('Unsupported data type.'))->during('send', [null]);
        $this->shouldThrow(new \InvalidArgumentException('Unsupported data type.'))->during('send', [new \stdClass()]);
        $this->shouldThrow(new \InvalidArgumentException('Unsupported data type.'))->during('send', [1]);
        $this->shouldThrow(new \InvalidArgumentException('Unsupported data type.'))->during('send', [true]);
    }

    public function it_can_dispatch_a_command_and_wait_for_the_result(Command $command, $commandBus)
    {
        $commandBus->dispatch($command)->shouldBeCalledTimes(1);
        $this->sendAndWait($command);
    }
}
