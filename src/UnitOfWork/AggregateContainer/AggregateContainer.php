<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;

interface AggregateContainer
{
    /**
     * @param AggregateRoot         $aggregateRoot
     * @param EventBus              $eventBus
     * @param SaveAggregateCallback $saveAggregateCallback
     *
     * @return AggregateRoot
     */
    public function add(AggregateRoot $aggregateRoot, EventBus $eventBus, SaveAggregateCallback $saveAggregateCallback);

    /**
     * @return array
     */
    public function all();

    /**
     * Triggers all saving callbacks on all registered aggregates.
     */
    public function saveAggregateRoots();

    /**
     * Clears all staged aggregate roots and their corresponding save callback.
     */
    public function clear();
}
