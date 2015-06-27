<?php

namespace RayRutjes\DomainFoundation\Repository\ConflictResolver;

use RayRutjes\DomainFoundation\Domain\Event\EventStream;

interface ConflictResolver
{
    /**
     * @param \RayRutjes\DomainFoundation\Domain\Event\EventStream $appliedEvents
     * @param EventStream                                          $committedEvents
     */
    public function resolverConflicts(EventStream $appliedEvents, EventStream $committedEvents);
}
