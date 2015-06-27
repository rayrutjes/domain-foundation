<?php

namespace spec\RayRutjes\DomainFoundation\Command\Factory;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericCommandFactorySpec extends ObjectBehavior
{
    public function let(ContractFactory $contractFactory, MessageIdentifierFactory $messageIdentifierFactory)
    {
        $this->beConstructedWith($contractFactory, $messageIdentifierFactory);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Factory\GenericCommandFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Command\Factory\CommandFactory');
    }

    public function it_creates_a_generic_command_from_a_payload(Serializable $payload, $contractFactory, $messageIdentifierFactory, Contract $contract, MessageIdentifier $messageIdentifier)
    {
        $contractFactory->createFromObject($payload)->willReturn($contract);
        $messageIdentifierFactory->generate()->willReturn($messageIdentifier);
        $this->createFromPayload($payload)->shouldHaveType('RayRutjes\DomainFoundation\Command\GenericCommand');
    }

    public function it_creates_a_generic_command(MessageIdentifier $messageIdentifier, Serializable $payload)
    {
        $this->create('commandName', $messageIdentifier, $payload);
    }
}
