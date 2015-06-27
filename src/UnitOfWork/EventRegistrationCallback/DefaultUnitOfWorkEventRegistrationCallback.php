<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

final class DefaultUnitOfWorkEventRegistrationCallback implements EventRegistrationCallback
{
    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @param UnitOfWork $unitOfWork
     * @param EventBus   $eventBus
     */
    public function __construct(UnitOfWork $unitOfWork, EventBus $eventBus)
    {
        $this->unitOfWork = $unitOfWork;
        $this->eventBus = $eventBus;
    }

    /**
     * @param Event $event
     *
     * @return Event
     */
    public function onEventRegistration(Event $event)
    {
        $this->unitOfWork->publishEvent($event, $this->eventBus);

        return $this->unitOfWork->invokeEventRegistrationListeners($event);
    }
}
