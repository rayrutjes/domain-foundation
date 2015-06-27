<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\AggregateContainer;
use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\DefaultAggregateContainer;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\DefaultUnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\UnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultAggregateContainerFactory implements AggregateContainerFactory
{
    /**
     * @param UnitOfWork                                 $unitOfWork
     * @param UnitOfWorkEventRegistrationCallbackFactory $eventRegistrationCallbackFactory
     *
     * @return AggregateContainer
     *
     * @internal param UnitOfWorkEventRegistrationCallbackFactory $unitOfWorkEventRegistrationCallbackFactory
     */
    public function create(
        UnitOfWork $unitOfWork,
        UnitOfWorkEventRegistrationCallbackFactory $eventRegistrationCallbackFactory = null
    ) {
        $eventRegistrationCallbackFactory = null === $eventRegistrationCallbackFactory ? new DefaultUnitOfWorkEventRegistrationCallbackFactory() : $eventRegistrationCallbackFactory;

        return new DefaultAggregateContainer($unitOfWork, $eventRegistrationCallbackFactory);
    }
}
