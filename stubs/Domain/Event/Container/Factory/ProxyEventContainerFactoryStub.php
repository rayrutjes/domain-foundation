<?php

namespace RayRutjes\DomainFoundation\Stub\Domain\Event\Container\Factory;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Container\Factory\EventContainerFactory;

class ProxyEventContainerFactoryStub implements EventContainerFactory
{
    /**
     * @var EventContainerFactory
     */
    private $eventContainerFactory;

    /**
     * @param EventContainerFactory $eventContainerFactory
     */
    public function proxy(EventContainerFactory $eventContainerFactory)
    {
        $this->eventContainerFactory = $eventContainerFactory;
    }

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventContainer
     */
    public function create(AggregateRootIdentifier $aggregateRootIdentifier)
    {
        return $this->eventContainerFactory->create($aggregateRootIdentifier);
    }
}
