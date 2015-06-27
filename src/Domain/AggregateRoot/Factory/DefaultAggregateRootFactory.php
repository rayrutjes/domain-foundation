<?php

namespace RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

final class DefaultAggregateRootFactory implements AggregateRootFactory
{
    /**
     * @param Contract    $aggregateRootType
     * @param EventStream $eventStream
     *
     * @return AggregateRoot
     */
    public function loadFromHistory(Contract $aggregateRootType, EventStream $eventStream)
    {
        $className = $aggregateRootType->className();

        return $className::loadFromHistory($eventStream);
    }
}
