<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory;

use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

interface UnitOfWorkEventRegistrationCallbackFactory
{
    /**
     * @param UnitOfWork $unitOfWork
     * @param EventBus   $eventBus
     *
     * @return EventRegistrationCallback
     */
    public function create(UnitOfWork $unitOfWork, EventBus $eventBus);
}
