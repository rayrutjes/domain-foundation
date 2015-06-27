<?php

namespace RayRutjes\DomainFoundation\EventBus;

use RayRutjes\DomainFoundation\EventBus\Listener\EventListener;

interface EventBus
{
    /**
     * @param array $events
     */
    public function publish(array $events);

    /**
     * @param EventListener $listener
     */
    public function subscribe(EventListener $listener);

    /**
     * @param EventListener $listener
     */
    public function unsubscribe(EventListener $listener);
}
