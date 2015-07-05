<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\EventBus\EventBus;

class AggregateRootContainer
{
    /**
     * @var array
     */
    private $aggregateRoots = [];

    /**
     * @var array
     */
    private $aggregateRootSaveCallbacks = [];

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @param AggregateRoot         $aggregateRoot
     * @param EventBus              $eventBus
     * @param SaveAggregateCallback $saveAggregateCallback
     *
     * @return AggregateRoot
     */
    public function add(AggregateRoot $aggregateRoot, EventBus $eventBus, SaveAggregateCallback $saveAggregateCallback)
    {
        $previouslyRegisteredAggregateRoot = $this->findSimilarAggregateRoot($aggregateRoot);
        if (null !== $previouslyRegisteredAggregateRoot) {
            return $previouslyRegisteredAggregateRoot;
        }

        $aggregateRootHash = $this->getAggregateRootHash($aggregateRoot);
        $this->aggregateRoots[$aggregateRootHash] = $aggregateRoot;
        $this->aggregateRootSaveCallbacks[$aggregateRootHash] = $saveAggregateCallback;

        $eventRegistrationCallback = new DefaultUnitOfWorkEventRegistrationCallback($this->unitOfWork, $eventBus);
        $aggregateRoot->addEventRegistrationCallback($eventRegistrationCallback);

        return $aggregateRoot;
    }

    /**
     * @return array
     */
    public function all()
    {
        return array_values($this->aggregateRoots);
    }

    /**
     * Triggers all saving callbacks on all staged aggregates.
     */
    public function saveAggregateRoots()
    {
        foreach ($this->aggregateRoots as $aggregateRootHash => $aggregateRoot) {
            $this->aggregateRootSaveCallbacks[$aggregateRootHash]->save($aggregateRoot);
        }
    }

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @return AggregateRoot|null
     */
    private function findSimilarAggregateRoot(AggregateRoot $aggregateRoot)
    {
        foreach ($this->aggregateRoots as $registeredAggregateRoot) {
            if ($registeredAggregateRoot->sameIdentityAs($aggregateRoot)) {
                return $registeredAggregateRoot;
            }
        }

        return;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @return string
     */
    private function getAggregateRootHash(AggregateRoot $aggregateRoot)
    {
        return spl_object_hash($aggregateRoot);
    }

    /**
     * Clears all staged aggregate roots and their corresponding save callback.
     */
    public function clear()
    {
        $this->aggregateRoots = [];
        $this->aggregateRootSaveCallbacks = [];
    }
}
