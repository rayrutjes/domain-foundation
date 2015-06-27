<?php

namespace spec\RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

class DefaultAggregateRootFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\DefaultAggregateRootFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\AggregateRootFactory');
    }

    public function it_can_load_an_aggregate_root_from_its_history(Contract $contract, EventStream $eventStream)
    {
        $contract->className()->willReturn('\RayRutjes\DomainFoundation\Stub\Domain\AggregateRoot\EventSourcedAggregateRootStub');
        $this->loadFromHistory($contract, $eventStream)->shouldHaveType('RayRutjes\DomainFoundation\Stub\Domain\AggregateRoot\EventSourcedAggregateRootStub');
    }
}
