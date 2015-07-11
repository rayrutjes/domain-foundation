<?php

namespace RayRutjes\DomainFoundation\EventBus;

use RayRutjes\DomainFoundation\Domain\Event\Event;

interface EventBus
{
    /**
     * @param Event ...$events
     */
    public function publish(/* HH_FIXME[4033]: variadic + strict */ ...$events);

    /**
     * @param EventListener $listener
     */
    public function subscribe(EventListener $listener);

    /**
     * @param EventListener $listener
     */
    public function unsubscribe(EventListener $listener);
}
