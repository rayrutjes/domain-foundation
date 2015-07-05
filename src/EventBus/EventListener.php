<?php

namespace RayRutjes\DomainFoundation\EventBus;

use RayRutjes\DomainFoundation\Domain\Event\Event;

interface EventListener
{
    /**
     * @param Event $event
     */
    public function handle(Event $event);
}
