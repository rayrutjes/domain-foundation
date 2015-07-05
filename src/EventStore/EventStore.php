<?php

namespace RayRutjes\DomainFoundation\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

interface EventStore
{
    /**
     * @param Contract    $aggregateRootType
     * @param EventStream $eventStream
     */
    public function append(Contract $aggregateRootType, EventStream $eventStream);

    /**
     * @param Contract                $aggregateRootType
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventStream
     */
    public function read(Contract $aggregateRootType, AggregateRootIdentifier $aggregateRootIdentifier);
}
