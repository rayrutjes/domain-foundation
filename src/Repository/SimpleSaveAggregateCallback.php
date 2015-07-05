<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback;

class SimpleSaveAggregateCallback implements SaveAggregateCallback
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
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
