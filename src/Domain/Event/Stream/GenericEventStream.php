<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Stream;

use RayRutjes\DomainFoundation\Domain\Event\Event;

final class GenericEventStream implements EventStream
{
    /**
     * @var array
     */
    private $events;

    /**
     * @var int
     */
    private $nextIndex = 0;

    /**
     * @param array $events
     */
    public function __construct(array $events = [])
    {
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                throw new \InvalidArgumentException('Stream should only contain Event objects.');
            }
        }
        $this->events = $events;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return count($this->events) > $this->nextIndex;
    }

    /**
     * @return Event
     */
    public function next()
    {
        if (!$this->hasNext()) {
            throw new \OutOfBoundsException('You reached the end of the stream.');
        }

        return $this->events[$this->nextIndex++];
    }

    /**
     * @return Event
     */
    public function peek()
    {
        if (!$this->hasNext()) {
            throw new \OutOfBoundsException('You reached the end of the stream.');
        }

        return $this->events[$this->nextIndex];
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return 0 === count($this->events);
    }
}
