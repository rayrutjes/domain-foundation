<?php

namespace RayRutjes\DomainFoundation\EventBus\Listener;

use RayRutjes\DomainFoundation\Domain\Event\Event;

interface EventListener
{
    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function handle(Event $event);
}
