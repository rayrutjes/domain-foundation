<?php

namespace RayRutjes\DomainFoundation\Stub\Domain\Event\Stream\Factory;

use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\EventStreamFactory;

class ProxyEventStreamFactoryStub implements EventStreamFactory
{
    /**
     * @var EventStreamFactory
     */
    private $eventStreamFactory;

    /**
     * @param EventStreamFactory $eventStreamFactory
     */
    public function proxy(EventStreamFactory $eventStreamFactory)
    {
        $this->eventStreamFactory = $eventStreamFactory;
    }

    /**
     * @param array $events
     *
     * @return EventStream
     */
    public function create(array $events = [])
    {
        return $this->eventStreamFactory->create($events);
    }
}
