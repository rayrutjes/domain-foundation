<?php

namespace RayRutjes\DomainFoundation\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

interface AggregateRoot
{
    /**
     * @return AggregateRootIdentifier
     */
    public function identifier();

    /**
     * Return all recorded events.
     *
     * @return EventStream
     */
    public function uncommittedChanges();

    /**
     * Clears all recorded events.
     */
    public function commitChanges();

    /**
     * @return int
     */
    public function lastCommittedEventSequenceNumber();

    /**
     * @param EventStream $eventStream
     *
     * @return AggregateRoot
     */
    public static function loadFromHistory(EventStream $eventStream);

    /**
     * @param EventRegistrationCallback $callback
     */
    public function addEventRegistrationCallback(EventRegistrationCallback $callback);

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @return mixed
     */
    public function sameIdentityAs(AggregateRoot $aggregateRoot);
}
