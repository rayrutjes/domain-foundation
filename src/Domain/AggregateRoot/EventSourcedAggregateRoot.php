<?php

namespace RayRutjes\DomainFoundation\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Container\Factory\DefaultEventContainerFactory;
use RayRutjes\DomainFoundation\Domain\Event\Container\Factory\EventContainerFactory;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\GenericEventStreamFactory;
use RayRutjes\DomainFoundation\Serializer\Serializable;

abstract class EventSourcedAggregateRoot implements AggregateRoot
{
    /**
     * @var AggregateRootIdentifier
     */
    private $identifier;

    /**
     * @var EventContainer
     */
    private $changes;

    /**
     * @var int
     */
    private $lastCommittedEventSequenceNumber;

    /**
     * Constructor will be used internally to reconstitute the state of the aggregate.
     * DDD encourages expressive static factory methods as a replacement.
     */
    final protected function __construct()
    {
    }

    /**
     * @return AggregateRootIdentifier
     */
    final public function identifier()
    {
        if (null === $this->identifier) {
            throw new \BadMethodCallException('Identifier has not been initialized.');
        }

        return $this->identifier;
    }

    /**
     * @param AggregateRootIdentifier $identifier
     */
    final protected function setIdentifier(AggregateRootIdentifier $identifier)
    {
        if (null !== $this->identifier) {
            throw new \BadMethodCallException('Identifier has already been set.');
        }
        $this->identifier = $identifier;
    }

    /**
     * Return all recorded events.
     *
     * @return EventStream
     */
    final public function uncommittedChanges()
    {
        if (null === $this->changes) {
            return $this->eventStreamFactory()->create();
        }

        return $this->changes->eventStream();
    }

    /**
     * Clears all recorded events.
     */
    final public function commitChanges()
    {
        if (null !== $this->changes) {
            $this->lastCommittedEventSequenceNumber = $this->changes->lastSequenceNumber();
            $this->changes->commit();
        }
    }

    /**
     * Mutate the state of the aggregate by applying a domain event.
     * Keep track of the change until it has been successfully committed.
     *
     * @param Serializable $payload
     */
    final protected function applyChange(Serializable $payload)
    {
        $this->apply($payload);
        $this->changes()->addEventFromPayload($payload);
    }

    /**
     * Mutate the state of the aggregate by applying the domain event contained into the message.
     * Synchronize the aggregate version with the one provided by the message.
     *
     * @param Event $event
     */
    final private function replayChange(Event $event)
    {
        $this->apply($event->payload());
        $this->lastCommittedEventSequenceNumber = $event->sequenceNumber();
    }

    /**
     * Mutate the state of the aggregate by applying a domain event.
     *
     * @param Serializable $payload
     */
    final private function apply(Serializable $payload)
    {
        $method = $this->applyMethod($payload);
        $this->$method($payload);
    }

    /**
     * Returns method to call for applying a given domain event to the aggregate.
     * This can be overridden to suit your custom naming convention.
     *
     * @param Serializable $payload
     *
     * @return string
     */
    protected function applyMethod(Serializable $payload)
    {
        $className = get_class($payload);

        return 'apply'.substr($className, strrpos($className, '\\') + 1);
    }

    /**
     * Return the event container containing the uncommitted changes.
     * If there are no pending changes to be committed, a new domain event stream is
     * resolved from a domain event stream factory.
     *
     * @return EventContainer
     */
    final private function changes()
    {
        if (null === $this->changes) {
            $lastCommittedEventSequenceNumber = $this->lastCommittedEventSequenceNumber();

            $this->changes = $this->eventContainerFactory()->create($this->identifier());

            // Todo: has this method says, initialization should be done at init.
            $this->changes->initializeSequenceNumber($lastCommittedEventSequenceNumber);
        }

        return $this->changes;
    }

    /**
     * Roll-out all events to reconstitute the state of the aggregate.
     *
     * @param EventStream $eventStream
     *
     * @return static
     */
    final public static function loadFromHistory(EventStream $eventStream)
    {
        $aggregate = new static();

        while ($eventStream->hasNext()) {
            $aggregate->replayChange($eventStream->next());
        }

        return $aggregate;
    }

    /**
     * @return int
     */
    final public function lastCommittedEventSequenceNumber()
    {
        if (null === $this->changes) {
            if (null === $this->lastCommittedEventSequenceNumber) {
                // todo: this actually an illegal state.
                return 0;
            }

            return $this->lastCommittedEventSequenceNumber;
        }

        return $this->changes()->lastCommittedSequenceNumber();
    }

     /**
      * @param EventRegistrationCallback $callback
      */
     final public function addEventRegistrationCallback(EventRegistrationCallback $callback)
     {
         $this->changes()->addRegistrationCallback($callback);
     }

     /**
      * @param AggregateRoot $aggregateRoot
      *
      * @return mixed
      */
     final public function sameIdentityAs(AggregateRoot $aggregateRoot)
     {
         if (!$aggregateRoot instanceof static) {
             return false;
         }

         return $this->identifier()->sameValueAs($aggregateRoot->identifier());
     }

    /**
     * @return GenericEventStreamFactory
     */
    protected function eventStreamFactory()
    {
        return new GenericEventStreamFactory();
    }

    /**
     * @return EventContainerFactory
     */
    protected function eventContainerFactory()
    {
        return new DefaultEventContainerFactory($this->eventStreamFactory());
    }
}
