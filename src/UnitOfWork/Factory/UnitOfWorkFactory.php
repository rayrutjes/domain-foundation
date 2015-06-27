<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

interface UnitOfWorkFactory
{
    /**
     * @return UnitOfWork
     */
    public function createUnitOfWork();
}
