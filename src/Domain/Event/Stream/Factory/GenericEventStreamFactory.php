<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Stream\Factory;

use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;

class GenericEventStreamFactory implements EventStreamFactory
{
    /**
     * @param array $events
     *
     * @return EventStream
     */
    public function create(array $events = [])
    {
        return new GenericEventStream($events);
    }
}
