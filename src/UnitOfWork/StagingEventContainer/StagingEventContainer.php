<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;

/**
 * This class acts as a container for events that will be published
 * through onto the associated event buses when the unit of work is committed.
 */
interface StagingEventContainer
{
    /**
     * @param Event    $event
     * @param EventBus $eventBus
     */
    public function add(Event $event, EventBus $eventBus);

    public function publishEvents();

    /**
     * @return array
     */
    public function all();

    public function clear();
}
