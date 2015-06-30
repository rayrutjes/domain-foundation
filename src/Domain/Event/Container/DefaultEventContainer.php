<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Container;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\EventStreamFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultEventContainer implements \Countable, EventContainer
{
    /**
     * @var array
     */
    private $events = [];

    /**
     * @var AggregateRootIdentifier
     */
    private $aggregateRootIdentifier;

    /**
     * @var int
     */
    private $lastCommittedSequenceNumber;

    /**
     * @var array
     */
    private $registrationCallbacks = [];

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var MessageIdentifierFactory
     */
    private $messageIdentifierFactory;

    /**
     * @var EventStreamFactory
     */
    private $eventStreamFactory;

    /**
     * @param AggregateRootIdentifier  $aggregateRootIdentifier
     * @param EventFactory             $eventFactory
     * @param MessageIdentifierFactory $messageIdentifierFactory
     * @param EventStreamFactory       $eventStreamFactory
     */
    public function __construct(
        AggregateRootIdentifier $aggregateRootIdentifier,
        EventFactory $eventFactory,
        MessageIdentifierFactory $messageIdentifierFactory,
        EventStreamFactory $eventStreamFactory
    ) {
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;
        $this->eventFactory = $eventFactory;
        $this->messageIdentifierFactory = $messageIdentifierFactory;
        $this->eventStreamFactory = $eventStreamFactory;
    }

    /**
     * @param Serializable $payload
     */
    public function addEventFromPayload(Serializable $payload)
    {
        $event = $this->eventFactory->create(
            $this->aggregateRootIdentifier,
            $this->nextSequenceNumber(),
            $this->messageIdentifierFactory->generate(),
            $payload
        );

        $this->addEvent($event);
    }

    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function addEvent(Event $event)
    {
        $expectedSequenceNumber = $this->nextSequenceNumber();
        if (null !== $expectedSequenceNumber && $expectedSequenceNumber !== $event->sequenceNumber()) {
            throw new \Exception('Corrupted sequence number.');
        }

        $this->events[] = $this->applyRegistrationCallbacks($event);
    }

    /**
     * @return EventStream
     */
    public function eventStream()
    {
        return $this->eventStreamFactory->create($this->events);
    }

    /**
     * @param int $lastKnownSequenceNumber
     */
    public function initializeSequenceNumber($lastKnownSequenceNumber)
    {
        if (false === is_int($lastKnownSequenceNumber) || $lastKnownSequenceNumber < 0) {
            throw new \InvalidArgumentException('Sequence number should be a positive integer.');
        }

        if (!empty($this->events)) {
            throw new \LogicException('Can not initialize sequence number if events have already been added.');
        }
        $this->lastCommittedSequenceNumber = $lastKnownSequenceNumber;
    }

    /**
     * @return int
     */
    private function nextSequenceNumber()
    {
        $currentSequenceNumber = $this->lastSequenceNumber();

        return null === $currentSequenceNumber ? 1 : $currentSequenceNumber + 1;
    }

    /**
     * @return int|null
     */
    public function lastSequenceNumber()
    {
        if (empty($this->events)) {
            return $this->lastCommittedSequenceNumber;
        }

        $event = end($this->events);

        return $event->sequenceNumber();
    }

    /**
     * @return int
     */
    public function lastCommittedSequenceNumber()
    {
        return $this->lastCommittedSequenceNumber;
    }

    public function commit()
    {
        $this->lastCommittedSequenceNumber = $this->lastSequenceNumber();
        $this->events = [];
        $this->registrationCallbacks = [];
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->events);
    }

    /**
     * @param EventRegistrationCallback $callback
     */
    public function addRegistrationCallback(EventRegistrationCallback $callback)
    {
        $this->registrationCallbacks[] = $callback;

        // Apply the callback on already registered events.
        $eventsCount = count($this->events);
        for ($i = 0; $i < $eventsCount; $i++) {
            $this->events[$i] = $callback->onEventRegistration($this->events[$i]);
        }
    }

    /**
     * @param Event $event
     *
     * @return Event
     */
    private function applyRegistrationCallbacks(Event $event)
    {
        foreach ($this->registrationCallbacks as $callback) {
            $event = $callback->onEventRegistration($event);
        }

        return $event;
    }
}
