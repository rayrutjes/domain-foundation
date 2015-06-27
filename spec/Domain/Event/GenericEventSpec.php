<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericEventSpec extends ObjectBehavior
{
    public function let(AggregateRootIdentifier $aggregateRootIdentifier, MessageIdentifier $identifier, Serializable $payload)
    {
        $this->beConstructedWith($aggregateRootIdentifier, 33, $identifier, $payload);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\GenericEvent');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Event');
    }

    public function it_can_return_its_aggregate_root_identifier($aggregateRootIdentifier)
    {
        $this->aggregateRootIdentifier()->shouldReturn($aggregateRootIdentifier);
    }

    public function it_can_return_its_sequence_number()
    {
        $this->sequenceNumber()->shouldReturn(33);
    }

    public function it_should_ensure_the_sequence_number_is_an_integer(AggregateRootIdentifier $aggregateRootIdentifier, MessageIdentifier $identifier, Serializable $payload)
    {
        $this->shouldThrow(new \InvalidArgumentException('Sequence number should be an integer.'))->during('__construct', [$aggregateRootIdentifier, '33', $identifier, $payload]);
    }
}
