<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\Listener;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

interface UnitOfWorkListener
{
    /**
     * @param UnitOfWork $unitOfWork
     */
    public function afterCommit(UnitOfWork $unitOfWork);

    /**
     * @param UnitOfWork $unitOfWork
     * @param \Exception $failureCause
     */
    public function onRollback(UnitOfWork $unitOfWork, \Exception $failureCause);

    /**
     * @param UnitOfWork $unitOfWork
     * @param Event      $event
     *
     * @return Event
     */
    public function onEventRegistration(UnitOfWork $unitOfWork, Event $event);

    /**
     * @param UnitOfWork $unitOfWork
     * @param array      $aggregates
     * @param array      $events
     */
    public function onPrepareCommit(UnitOfWork $unitOfWork, array $aggregates, array $events);

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function onPrepareTransactionCommit(UnitOfWork $unitOfWork);

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function onCleanup(UnitOfWork $unitOfWork);
}
