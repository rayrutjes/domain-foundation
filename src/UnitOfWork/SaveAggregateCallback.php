<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;

interface SaveAggregateCallback
{
    /**
     * @param AggregateRoot $aggregate
     */
    public function save(AggregateRoot $aggregate);
}
