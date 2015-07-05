<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener;

interface UnitOfWork
{
    public function start();

    public function commit();

    /**
     * @param \Exception $failureCause
     */
    public function rollback(\Exception $failureCause);

    /**
     * @param UnitOfWorkListener $listener
     */
    public function registerListener(UnitOfWorkListener $listener);

    /**
     * @param AggregateRoot         $aggregate
     * @param EventBus              $eventBus
     * @param SaveAggregateCallback $saveAggregateCallback
     *
     * @return AggregateRoot
     */
    public function registerAggregate(AggregateRoot $aggregate, EventBus $eventBus, SaveAggregateCallback $saveAggregateCallback);

    /**
     * @param Event    $event
     * @param EventBus $eventBus
     */
    public function publishEvent(Event $event, EventBus $eventBus);

    /**
     * @internal
     *
     * @param Event $event
     */
    public function invokeEventRegistrationListeners(Event $event);
}
