<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Container;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

final class DefaultEventContainer implements EventContainer
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
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param int                     $lastCommittedSequenceNumber
     */
    public function __construct(AggregateRootIdentifier $aggregateRootIdentifier, $lastCommittedSequenceNumber = 0)
    {
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;

        if (!is_int($lastCommittedSequenceNumber) || $lastCommittedSequenceNumber < 0) {
            throw new \InvalidArgumentException('Sequence number should be a positive integer.');
        }
        $this->lastCommittedSequenceNumber = $lastCommittedSequenceNumber;
    }

    /**
     * @param Serializable $payload
     */
    public function addEventFromPayload(Serializable $payload)
    {
        $event = new GenericEvent($this->aggregateRootIdentifier, $this->nextSequenceNumber(), MessageIdentifier::generate(), $payload);

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
        if ($expectedSequenceNumber !== $event->sequenceNumber()) {
            throw new \RuntimeException('Corrupted sequence number.');
        }

        $this->events[] = $this->applyRegistrationCallbacks($event);
    }

    /**
     * @return EventStream
     */
    public function eventStream()
    {
        return new GenericEventStream($this->events);
    }

    /**
     * @return int
     */
    private function nextSequenceNumber()
    {
        return $this->lastSequenceNumber() + 1;
    }

    /**
     * @return int
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
