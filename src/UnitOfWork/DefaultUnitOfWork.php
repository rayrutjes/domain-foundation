<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\AggregateContainer;
use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\Factory\AggregateContainerFactory;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\UnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;
use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory\StagingEventContainerFactory;
use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\StagingEventContainer;

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
     * @var AggregateContainer
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
     * @param UnitOfWorkListenerCollection               $listeners
     * @param StagingEventContainerFactory               $stagingEventContainerFactory
     * @param AggregateContainerFactory                  $aggregateContainerFactory
     * @param UnitOfWorkEventRegistrationCallbackFactory $unitOfWorkEventRegistrationCallbackFactory
     * @param TransactionManager                         $transactionManager
     */
    public function __construct(
        UnitOfWorkListenerCollection $listeners,
        StagingEventContainerFactory $stagingEventContainerFactory,
        AggregateContainerFactory $aggregateContainerFactory,
        UnitOfWorkEventRegistrationCallbackFactory $unitOfWorkEventRegistrationCallbackFactory,
        TransactionManager $transactionManager = null
    ) {
        $this->listeners = $listeners;
        $this->stagingEvents = $stagingEventContainerFactory->create();
        $this->registeredAggregates = $aggregateContainerFactory->create($this, $unitOfWorkEventRegistrationCallbackFactory);
        $this->transactionManager = $transactionManager;
    }

    private function isTransactional()
    {
        return null !== $this->transactionManager;
    }

    public function start()
    {
        if ($this->hasStarted()) {
            throw new \Exception('The unit of work has already been started.');
        }

        $this->hasStarted = true;

        if ($this->isTransactional()) {
            $this->transactionManager->startTransaction();
        }
    }

    public function stop()
    {
        $this->hasStarted = false;
    }

    /**
     * @return bool
     */
    private function hasStarted()
    {
        return $this->hasStarted;
    }

    public function commit()
    {
        if (!$this->hasStarted()) {
            throw new \Exception('The unit of work has not been started.');
        }

        try {
            $this->listeners->onPrepareCommit($this, $this->registeredAggregates->all(), $this->stagingEvents->all());
            $this->registeredAggregates->saveAggregateRoots();
            $this->stagingEvents->publishEvents();
            if ($this->isTransactional()) {
                $this->listeners->onPrepareTransactionCommit($this);
                $this->transactionManager->commitTransaction();
            }
            $this->listeners->afterCommit($this);
        } catch (\Exception $exception) {
            $this->rollback($exception);
            throw $exception;
        } finally {
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

    /**
     * @param AggregateRoot         $aggregate
     * @param EventBus              $eventBus
     * @param SaveAggregateCallback $saveAggregateCallback
     *
     * @return AggregateRoot
     */
    public function registerAggregate(AggregateRoot $aggregate, EventBus $eventBus, SaveAggregateCallback $saveAggregateCallback)
    {
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
}
