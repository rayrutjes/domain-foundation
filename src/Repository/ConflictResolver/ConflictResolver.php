<?php

namespace RayRutjes\DomainFoundation\Repository\ConflictResolver;

use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

interface ConflictResolver
{
    /**
     * @param EventStream $appliedEvents
     * @param EventStream $committedEvents
     */
    public function resolveConflicts(EventStream $appliedEvents, EventStream $committedEvents);
}
