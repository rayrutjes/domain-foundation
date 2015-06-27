<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;

class SimpleSaveAggregateCallback implements SaveAggregateCallback
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
     * @param AggregateRoot $aggregate
     */
    public function save(AggregateRoot $aggregate)
    {
        $this->repository->doSave($aggregate);
        $aggregate->commitChanges();
    }
}
