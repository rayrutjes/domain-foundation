<?php

namespace spec\RayRutjes\DomainFoundation\Command;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericCommandSpec extends ObjectBehavior
{
    public function let(MessageIdentifier $identifier, Serializable $payload)
    {
        $this->beConstructedWith('commandName', $identifier, $payload);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\GenericCommand');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Command');
    }

    public function it_can_return_its_command_name()
    {
        $this->commandName()->shouldReturn('commandName');
    }
}
