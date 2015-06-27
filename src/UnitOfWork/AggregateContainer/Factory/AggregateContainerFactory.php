<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\AggregateContainer;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\UnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

interface AggregateContainerFactory
{
    /**
     * @param UnitOfWork                                 $unitOfWork
     * @param UnitOfWorkEventRegistrationCallbackFactory $unitOfWorkEventRegistrationCallbackFactory
     *
     * @return AggregateContainer
     */
    public function create(UnitOfWork $unitOfWork, UnitOfWorkEventRegistrationCallbackFactory $unitOfWorkEventRegistrationCallbackFactory = null);
}
