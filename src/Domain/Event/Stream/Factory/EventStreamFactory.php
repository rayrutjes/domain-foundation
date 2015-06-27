<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Stream\Factory;

use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

interface EventStreamFactory
{
    /**
     * @param array $events
     *
     * @return EventStream
     */
    public function create(array $events = []);
}
