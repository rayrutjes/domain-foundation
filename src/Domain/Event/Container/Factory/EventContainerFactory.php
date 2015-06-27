<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Container\Factory;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer;

interface EventContainerFactory
{
    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventContainer
     */
    public function create(AggregateRootIdentifier $aggregateRootIdentifier);
}
