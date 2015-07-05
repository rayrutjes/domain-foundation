<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

final class AggregateRootRepository implements Repository
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
     * @var EventBus
     */
    private $eventBus;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @param UnitOfWork $unitOfWork
     * @param Contract   $aggregateRootType
     * @param EventStore $eventStore
     * @param EventBus   $eventBus
     */
    public function __construct(
        UnitOfWork $unitOfWork,
        Contract $aggregateRootType,
        EventStore $eventStore,
        EventBus $eventBus
    ) {
        $this->unitOfWork = $unitOfWork;
        $this->aggregateRootType = $aggregateRootType;
        $this->eventStore = $eventStore;
        $this->eventBus = $eventBus;
    }

    /**
     * @param AggregateRoot $aggregateRoot
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
            $this->createSaveAggregateCallback()
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

        $className = $this->aggregateRootType->className();
        $aggregateRoot = $className::loadFromHistory($eventStream);

        $actualVersion = $aggregateRoot->lastCommittedEventSequenceNumber();
        if (null !== $expectedVersion && $actualVersion !== $expectedVersion) {
            throw new ConflictingAggregateVersionException($aggregateRootIdentifier, $actualVersion, $expectedVersion);
        }

        return $this->unitOfWork->registerAggregate(
            $aggregateRoot,
            $this->eventBus,
            $this->createSaveAggregateCallback()
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

        if (!$eventStream->isEmpty()) {
            $this->eventStore->append($this->aggregateRootType, $eventStream);
        }
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

    /**
     * @return SimpleSaveAggregateCallback
     */
    private function createSaveAggregateCallback()
    {
        return new SimpleSaveAggregateCallback($this);
    }
}
