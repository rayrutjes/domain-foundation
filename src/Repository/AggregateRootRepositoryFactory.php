<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\AggregateRootFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\DefaultAggregateRootFactory;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\EventBus\SimpleEventBus;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\Factory\SaveAggregateCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class AggregateRootRepositoryFactory
{
    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var AggregateRootFactory
     */
    private $aggregateRootFactory;

    /**
     * @var SaveAggregateCallbackFactory
     */
    private $saveAggregateCallbackFactory;

    /**
     * @param EventStore                   $eventStore
     * @param EventBus                     $eventBus
     * @param AggregateRootFactory         $aggregateRootFactory
     * @param SaveAggregateCallbackFactory $saveAggregateCallbackFactory
     */
    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus = null,
        AggregateRootFactory $aggregateRootFactory = null,
        SaveAggregateCallbackFactory $saveAggregateCallbackFactory = null
    ) {
        $this->eventStore = $eventStore;
        $this->eventBus = null === $eventBus ? new SimpleEventBus() : $eventBus;
        $this->aggregateRootFactory = null === $aggregateRootFactory ? new DefaultAggregateRootFactory() : $aggregateRootFactory;
        $this->saveAggregateCallbackFactory = $saveAggregateCallbackFactory;
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @param Contract   $aggregateRootType
     *
     * @return AggregateRootRepository
     */
    public function create(UnitOfWork $unitOfWork, Contract $aggregateRootType)
    {
        $repository = new AggregateRootRepository(
            $unitOfWork,
            $aggregateRootType,
            $this->eventStore,
            $this->eventBus,
            $this->aggregateRootFactory
        );

        if (null === $this->saveAggregateCallbackFactory) {
            $saveCallbackFactory = new SimpleSaveAggregateCallbackFactory($repository);
            $repository->setSaveAggregateCallbackFactory($saveCallbackFactory);
        } else {
            $repository->setSaveAggregateCallbackFactory($this->saveAggregateCallbackFactory);
        }

        return $repository;
    }
}
