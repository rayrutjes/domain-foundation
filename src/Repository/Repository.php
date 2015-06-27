<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;

interface Repository
{
    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @throws ConflictingChangesException
     */
    public function add(AggregateRoot $aggregateRoot);

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param int                     $expectedVersion
     *
     * @return AggregateRoot
     *
     * @throws AggregateNotFoundException
     */
    public function load(AggregateRootIdentifier $aggregateRootIdentifier, $expectedVersion = null);

    /**
     * @internal
     *
     * @param AggregateRoot $aggregateRoot
     */
    public function doSave(AggregateRoot $aggregateRoot);
}
