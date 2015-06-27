<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;

interface SaveAggregateCallbackFactory
{
    /**
     * @return SaveAggregateCallback
     */
    public function create();
}
