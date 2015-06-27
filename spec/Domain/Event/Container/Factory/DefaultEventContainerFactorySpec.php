<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Container\Factory;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;

class DefaultEventContainerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Container\Factory\DefaultEventContainerFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Container\Factory\EventContainerFactory');
    }

    public function it_creates_an_event_container(AggregateRootIdentifier $aggregateRootIdentifier)
    {
        $this->create($aggregateRootIdentifier)->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer');
    }
}
