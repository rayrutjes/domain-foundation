<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

class ConflictingChangesException extends \RuntimeException
{
    /**
     * @var EventStream
     */
    private $conflictingEventStream;

    /**
     * @var EventStream
     */
    private $committedEventStream;

    /**
     * @param EventStream $conflictingEventStream
     * @param EventStream $committedEventStream
     */
    public function __construct(EventStream $conflictingEventStream, EventStream $committedEventStream)
    {
        $this->conflictingEventStream = $conflictingEventStream;
        $this->committedEventStream = $committedEventStream;

        parent::__construct('There are events conflicting with the committed domain event stream.');
    }

    /**
     * The partial stream of events conflicting with the committed events.
     *
     * @return EventStream
     */
    public function conflictingEventStream()
    {
        return $this->conflictingEventStream;
    }

    /**
     * The stream that could not be persisted.
     *
     * @return EventStream
     */
    public function committedEventStream()
    {
        return $this->committedEventStream;
    }
}
