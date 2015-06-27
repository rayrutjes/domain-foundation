<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\StagingEventContainer;

interface StagingEventContainerFactory
{
    /**
     * @return StagingEventContainer
     */
    public function create();
}
