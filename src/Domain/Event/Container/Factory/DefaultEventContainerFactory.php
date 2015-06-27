<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Container\Factory;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Container\DefaultEventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Factory\GenericEventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\EventStreamFactory;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\GenericEventStreamFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\UuidMessageIdentifierFactory;

class DefaultEventContainerFactory implements EventContainerFactory
{
    /**
     * @var EventStreamFactory
     */
    private $eventStreamFactory;

    /**
     * @var MessageIdentifierFactory
     */
    private $messageIdentifierFactory;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @param EventStreamFactory       $eventStreamFactory
     * @param MessageIdentifierFactory $messageIdentifierFactory
     * @param EventFactory             $eventFactory
     */
    public function __construct(
        EventStreamFactory $eventStreamFactory = null,
        MessageIdentifierFactory $messageIdentifierFactory = null,
        EventFactory $eventFactory = null
    ) {
        $this->eventStreamFactory = null === $eventStreamFactory ? new GenericEventStreamFactory() : $eventStreamFactory;
        $this->messageIdentifierFactory = null === $messageIdentifierFactory ? new UuidMessageIdentifierFactory() : $messageIdentifierFactory;
        $this->eventFactory = null === $eventFactory ? new GenericEventFactory() : $eventFactory;
    }

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventContainer
     */
    public function create(AggregateRootIdentifier $aggregateRootIdentifier)
    {
        return new DefaultEventContainer(
            $aggregateRootIdentifier,
            $this->eventFactory,
            $this->messageIdentifierFactory,
            $this->eventStreamFactory
        );
    }
}
