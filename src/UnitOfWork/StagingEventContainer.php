<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;

/**
 * This class acts as a container for events that will be published
 * through onto the associated event buses when the unit of work is committed.
 */
class StagingEventContainer
{
    /**
     * @var array
     */
    private $eventBuses = [];

    /**
     * @var array
     */
    private $events = [];

    /**
     * @param Event    $event
     * @param EventBus $eventBus
     */
    public function add(Event $event, EventBus $eventBus)
    {
        $eventBusHash = spl_object_hash($eventBus);
        $this->eventBuses[$eventBusHash] = $eventBus;
        $this->events[$eventBusHash][] = $event;
    }

    public function publishEvents()
    {
        $events = $this->events;
        foreach ($this->eventBuses as $eventBusHash => $eventBus) {
            $eventBus->publish(...$events[$eventBusHash]);
            $this->events[$eventBusHash] = [];
        }
    }

    public function clear()
    {
        $this->events = [];
        $this->eventBuses = [];
    }

    /**
     * @return array
     */
    public function all()
    {
        $events = [];
        foreach ($this->events as $eventBusEvents) {
            $events = array_merge($events, $eventBusEvents);
        }

        return $events;
    }
}
