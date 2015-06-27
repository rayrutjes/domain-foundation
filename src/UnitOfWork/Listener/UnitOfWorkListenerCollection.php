<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\Listener;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class UnitOfWorkListenerCollection implements UnitOfWorkListener
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param UnitOfWorkListener $listener
     */
    public function add(UnitOfWorkListener $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function afterCommit(UnitOfWork $unitOfWork)
    {
        $reversedListeners = array_reverse($this->listeners);
        foreach ($reversedListeners as $listener) {
            $listener->afterCommit($unitOfWork);
        }
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @param \Exception $failureCause
     */
    public function onRollback(UnitOfWork $unitOfWork, \Exception $failureCause)
    {
        $reversedListeners = array_reverse($this->listeners);
        foreach ($reversedListeners as $listener) {
            $listener->onRollback($unitOfWork, $failureCause);
        }
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @param Event      $event
     *
     * @return Event
     */
    public function onEventRegistration(UnitOfWork $unitOfWork, Event $event)
    {
        foreach ($this->listeners as $listener) {
            $event = $listener->onEventRegistration($unitOfWork, $event);
        }

        return $event;
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @param array      $aggregates
     * @param array      $events
     */
    public function onPrepareCommit(UnitOfWork $unitOfWork, array $aggregates, array $events)
    {
        foreach ($this->listeners as $listener) {
            $listener->onPrepareCommit($unitOfWork, $aggregates, $events);
        }
    }

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function onPrepareTransactionCommit(UnitOfWork $unitOfWork)
    {
        foreach ($this->listeners as $listener) {
            $listener->onPrepareTransactionCommit($unitOfWork);
        }
    }

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function onCleanup(UnitOfWork $unitOfWork)
    {
        $reversedListeners = array_reverse($this->listeners);
        foreach ($reversedListeners as $listener) {
            $listener->onCleanup($unitOfWork);
        }
    }
}
