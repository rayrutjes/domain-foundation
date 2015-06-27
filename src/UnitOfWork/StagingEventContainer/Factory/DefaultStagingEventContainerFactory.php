<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\DefaultStagingEventContainer;
use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\StagingEventContainer;

class DefaultStagingEventContainerFactory implements StagingEventContainerFactory
{
    /**
     * @return StagingEventContainer
     */
    public function create()
    {
        return new DefaultStagingEventContainer();
    }
}
