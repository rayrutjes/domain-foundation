<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection;

final class DefaultUnitOfWork implements UnitOfWork
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @var bool
     */
    private $hasStarted = false;

    /**
     * @var AggregateRootContainer
     */
    private $registeredAggregates;

    /**
     * @var StagingEventContainer
     */
    private $stagingEvents;

    /**
     * @var UnitOfWorkListenerCollection
     */
    private $listeners;

    /**
     * @param TransactionManager $transactionManager
     */
    public function __construct(TransactionManager $transactionManager = null)
    {
        $this->listeners = new UnitOfWorkListenerCollection();
        $this->stagingEvents = new StagingEventContainer();
        $this->registeredAggregates = new AggregateRootContainer($this);
        $this->transactionManager = $transactionManager;
    }

    public function start()
    {
        if ($this->hasStarted()) {
            throw new \RuntimeException('The unit of work has already been started.');
        }

        $this->hasStarted = true;

        $this->startTransaction();
    }

    /**
     * @return bool
     */
    private function hasStarted()
    {
        return $this->hasStarted;
    }

    private function isTransactional()
    {
        return null !== $this->transactionManager;
    }

    public function commit()
    {
        $this->assertHasStarted();

        try {
            $this->listeners->onPrepareCommit($this, $this->registeredAggregates->all(), $this->stagingEvents->all());
            $this->registeredAggregates->saveAggregateRoots();
            $this->stagingEvents->publishEvents();
            $this->commitTransaction();
            $this->listeners->afterCommit($this);
        } catch (\RuntimeException $exception) {
            $this->rollback($exception);
            throw $exception;
        } finally {
            $this->registeredAggregates->clear();
            $this->stagingEvents->clear();
            $this->stop();
            $this->listeners->onCleanup($this);
        }
    }

    /**
     * @param \Exception $failureCause
     */
    public function rollback(\Exception $failureCause)
    {
        $this->registeredAggregates->clear();
        $this->stagingEvents->clear();

        try {
            if ($this->isTransactional()) {
                $this->transactionManager->rollbackTransaction();
            }
        } finally {
            $this->listeners->onRollback($this, $failureCause);
        }
    }

    public function stop()
    {
        $this->hasStarted = false;
    }

    /**
     * @param AggregateRoot         $aggregate
     * @param EventBus              $eventBus
     * @param SaveAggregateCallback $saveAggregateCallback
     *
     * @return AggregateRoot
     */
    public function registerAggregate(
        AggregateRoot $aggregate,
        EventBus $eventBus,
        SaveAggregateCallback $saveAggregateCallback
    ) {
        return $this->registeredAggregates->add($aggregate, $eventBus, $saveAggregateCallback);
    }

    /**
     * @param Event $event
     *
     * @return Event
     *
     * @internal
     */
    public function invokeEventRegistrationListeners(Event $event)
    {
        return $this->listeners->onEventRegistration($this, $event);
    }

    /**
     * @param Event    $event
     * @param EventBus $eventBus
     */
    public function publishEvent(Event $event, EventBus $eventBus)
    {
        $this->stagingEvents->add($event, $eventBus);
    }

    /**
     * @param UnitOfWorkListener $listener
     */
    public function registerListener(UnitOfWorkListener $listener)
    {
        $this->listeners->add($listener);
    }

    private function assertHasStarted()
    {
        if (!$this->hasStarted()) {
            throw new \RuntimeException('The unit of work has not been started.');
        }
    }

    private function commitTransaction()
    {
        if ($this->isTransactional()) {
            $this->listeners->onPrepareTransactionCommit($this);
            $this->transactionManager->commitTransaction();
        }
    }

    private function startTransaction()
    {
        if ($this->isTransactional()) {
            $this->transactionManager->startTransaction();
        }
    }
}
