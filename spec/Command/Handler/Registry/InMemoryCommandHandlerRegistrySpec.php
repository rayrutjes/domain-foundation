<?php

namespace spec\RayRutjes\DomainFoundation\Command\Handler\Registry;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;
use RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerNotFoundException;

class InMemoryCommandHandlerRegistrySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Handler\Registry\InMemoryCommandHandlerRegistry');
    }

    public function it_should_throw_an_exception_if_no_handler_has_been_found_for_a_command(Command $command)
    {
        $this->shouldThrow(new CommandHandlerNotFoundException())->during('findCommandHandlerFor', [$command]);
    }

    public function it_can_subscribe_a_new_command_handler(CommandHandler $handler)
    {
        $this->subscribe('commandName', $handler);
    }

    public function it_can_find_a_command_handler_for_a_command(Command $command, CommandHandler $handler)
    {
        $command->commandName()->willReturn('commandName');
        $this->subscribe('commandName', $handler);
        $this->findCommandHandlerFor($command)->shouldReturn($handler);
    }
}
