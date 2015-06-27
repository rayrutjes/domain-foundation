<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory;

use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\DefaultUnitOfWorkEventRegistrationCallback;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultUnitOfWorkEventRegistrationCallbackFactory implements UnitOfWorkEventRegistrationCallbackFactory
{
    /**
     * @param UnitOfWork $unitOfWork
     * @param EventBus   $eventBus
     *
     * @return EventRegistrationCallback
     */
    public function create(UnitOfWork $unitOfWork, EventBus $eventBus)
    {
        return new DefaultUnitOfWorkEventRegistrationCallback($unitOfWork, $eventBus);
    }
}
