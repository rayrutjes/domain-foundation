<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\AggregateRootFactory;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\Factory\SaveAggregateCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class AggregateRootRepository implements Repository
{
    /**
     * @var Contract
     */
    private $aggregateRootType;

    /**
     * @var EventStore
     */
    private $eventStore;

    /**
     * @var AggregateRootFactory
     */
    private $aggregateRootFactory;

    /**
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var SaveAggregateCallbackFactory
     */
    private $saveAggregateCallbackFactory;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @param UnitOfWork                   $unitOfWork
     * @param Contract                     $aggregateRootType
     * @param EventStore                   $eventStore
     * @param EventBus                     $eventBus
     * @param AggregateRootFactory         $aggregateRootFactory
     * @param SaveAggregateCallbackFactory $saveAggregateCallbackFactory
     */
    public function __construct(
        UnitOfWork $unitOfWork,
        Contract $aggregateRootType,
        EventStore $eventStore,
        EventBus $eventBus,
        AggregateRootFactory $aggregateRootFactory,
        SaveAggregateCallbackFactory $saveAggregateCallbackFactory
    ) {
        $this->unitOfWork = $unitOfWork;
        $this->aggregateRootType = $aggregateRootType;
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
        $this->aggregateRootFactory = $aggregateRootFactory;
        $this->saveAggregateCallbackFactory = $saveAggregateCallbackFactory;
    }

    /**
     * @param AggregateRoot $aggregateRoot
     *
     * @throws ConflictingChangesException
     */
    public function add(AggregateRoot $aggregateRoot)
    {
        $this->ensureAggregateRootIsSupported($aggregateRoot);
        if ($aggregateRoot->lastCommittedEventSequenceNumber() !== 0) {
            throw new \InvalidArgumentException('Only new aggregates can be added to the repository.');
        }

        $this->unitOfWork->registerAggregate(
            $aggregateRoot,
            $this->eventBus,
            $this->saveAggregateCallbackFactory->create()
        );
    }

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param int                     $expectedVersion
     *
     * @return AggregateRoot
     *
     * @throws AggregateNotFoundException
     */
    public function load(AggregateRootIdentifier $aggregateRootIdentifier, $expectedVersion = null)
    {
        $eventStream = $this->eventStore->read($this->aggregateRootType, $aggregateRootIdentifier);
        if ($eventStream->isEmpty()) {
            throw new AggregateNotFoundException($aggregateRootIdentifier);
        }

        $aggregateRoot = $this->aggregateRootFactory->loadFromHistory($this->aggregateRootType, $eventStream);

        if (null !== $expectedVersion) {
            $actualVersion = $aggregateRoot->lastCommittedEventSequenceNumber();
            if ($actualVersion !== $expectedVersion) {
                throw new ConflictingAggregateVersionException($aggregateRootIdentifier, $actualVersion, $expectedVersion);
            }
        }

        return $this->unitOfWork->registerAggregate(
            $aggregateRoot,
            $this->eventBus,
            $this->saveAggregateCallbackFactory->create()
        );
    }

    /**
     * @internal
     *
     * @param AggregateRoot $aggregateRoot
     */
    public function doSave(AggregateRoot $aggregateRoot)
    {
        $eventStream = $aggregateRoot->uncommittedChanges();

        if ($eventStream->isEmpty()) {
            return;
        }

        $this->eventStore->append($this->aggregateRootType, $eventStream);
    }

    /**
     * @param AggregateRoot $aggregateRoot
     */
    private function ensureAggregateRootIsSupported(AggregateRoot $aggregateRoot)
    {
        $supportedClassName = $this->aggregateRootType->className();
        if (!$aggregateRoot instanceof $supportedClassName) {
            throw new \InvalidArgumentException('Unsupported aggregate type.');
        }
    }
}
