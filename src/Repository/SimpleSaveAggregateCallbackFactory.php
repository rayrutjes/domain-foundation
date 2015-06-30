<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\Factory\SaveAggregateCallbackFactory;

class SimpleSaveAggregateCallbackFactory implements SaveAggregateCallbackFactory
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    /**
     * @param AggregateRootRepository $repository
     */
    public function __construct(AggregateRootRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return SimpleSaveAggregateCallback
     */
    public function create()
    {
        return new SimpleSaveAggregateCallback($this->repository);
    }
}
