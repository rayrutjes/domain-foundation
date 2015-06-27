<?php

namespace RayRutjes\DomainFoundation\Domain\Event;

interface EventRegistrationCallback
{
    /**
     * @param Event $event
     *
     * @return Event
     */
    public function onEventRegistration(Event $event);
}
