<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Factory;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericEventFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Factory\GenericEventFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory');
    }

    public function it_creates_generic_events(
        AggregateRootIdentifier $aggregateRootIdentifier,
        MessageIdentifier $identifier,
        Serializable $payload,
        Metadata $metadata,
        ContractFactory $contractFactory
    ) {
        $this->create($aggregateRootIdentifier, 1, $identifier, $payload, $metadata, $contractFactory);
    }
}
