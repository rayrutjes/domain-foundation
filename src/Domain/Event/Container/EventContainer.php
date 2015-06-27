<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Container;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Serializer\Serializable;

interface EventContainer
{
    /**
     * @param Serializable $payload
     */
    public function addEventFromPayload(Serializable $payload);

    /**
     * @param Event $event
     *
     * @throws \Exception
     */
    public function addEvent(Event $event);

    /**
     * @return EventStream
     */
    public function eventStream();

    /**
     * @param $lastKnownSequenceNumber
     */
    public function initializeSequenceNumber($lastKnownSequenceNumber);

    /**
     * @return int|null
     */
    public function lastSequenceNumber();

    /**
     * @return int|null
     */
    public function lastCommittedSequenceNumber();

    public function commit();

    /**
     * @return int
     */
    public function count();

    /**
     * @param EventRegistrationCallback $callback
     */
    public function addRegistrationCallback(EventRegistrationCallback $callback);
}
