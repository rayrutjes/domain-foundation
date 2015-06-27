<?php

namespace RayRutjes\DomainFoundation\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

interface EventStore
{
    /**
     * @param Contract    $aggregateType
     * @param EventStream $eventStream
     */
    public function append(Contract $aggregateType, EventStream $eventStream);

    /**
     * @param Contract                $aggregateType
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventStream
     */
    public function read(Contract $aggregateType, AggregateRootIdentifier $aggregateRootIdentifier);
}
