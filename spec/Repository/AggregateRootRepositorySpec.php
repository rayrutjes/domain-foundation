<?php

namespace spec\RayRutjes\DomainFoundation\Repository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\Factory\AggregateRootFactory;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\Repository\AggregateNotFoundException;
use RayRutjes\DomainFoundation\Repository\ConflictingAggregateVersionException;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\Factory\SaveAggregateCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class AggregateRootRepositorySpec extends ObjectBehavior
{
    public function let(
        UnitOfWork $unitOfWork,
        Contract $aggregateRootType,
        EventStore $eventStore,
        EventBus $eventBus,
        AggregateRootFactory $aggregateRootFactory,
        SaveAggregateCallbackFactory $saveAggregateCallbackFactory,
        SaveAggregateCallback $saveAggregateCallback
    ) {
        $this->beConstructedWith(
            $unitOfWork,
            $aggregateRootType,
            $eventStore,
            $eventBus,
            $aggregateRootFactory
        );

        $aggregateRootType->className()->willReturn('RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot');
        $saveAggregateCallbackFactory->create()->willReturn($saveAggregateCallback);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Repository\AggregateRootRepository');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Repository\Repository');
    }

    public function it_can_register_a_new_aggregate_root(
        AggregateRoot $aggregateRoot,
        $eventBus,
        $saveAggregateCallbackFactory,
        SaveAggregateCallback $saveAggregateCallback,
        UnitOfWork $unitOfWork
    ) {
        $this->setSaveAggregateCallbackFactory($saveAggregateCallbackFactory);

        $aggregateRoot->lastCommittedEventSequenceNumber()->willReturn(0);

        $saveAggregateCallbackFactory->create()->willReturn($saveAggregateCallback);
        $unitOfWork->registerAggregate($aggregateRoot, $eventBus, $saveAggregateCallback)->shouldBeCalledTimes(1);

        $this->add($aggregateRoot);
    }

    public function it_can_not_register_already_committed_aggregate_roots(AggregateRoot $aggregateRoot)
    {
        $aggregateRoot->lastCommittedEventSequenceNumber()->willReturn(1);
        $this->shouldThrow(new \InvalidArgumentException('Only new aggregates can be added to the repository.'))->during('add', [$aggregateRoot]);
    }

    public function it_should_ensure_the_aggregate_root_type_is_supported(AggregateRoot $aggregateRoot, $aggregateRootType)
    {
        $aggregateRootType->className()->willReturn('Fake\Expectation\Class\Name');
        $this->shouldThrow(new \InvalidArgumentException('Unsupported aggregate type.'))->during('add', [$aggregateRoot]);
    }

    public function it_can_load_an_existing_aggregate_root(
        AggregateRootIdentifier $identifier,
        $eventStore,
        $aggregateRootType,
        $aggregateRootFactory,
        EventStream $eventStream,
        AggregateRoot $aggregateRoot,
        $unitOfWork,
        $eventBus,
        $saveAggregateCallbackFactory,
        $saveAggregateCallback
    ) {
        $this->setSaveAggregateCallbackFactory($saveAggregateCallbackFactory);

        $eventStore->read($aggregateRootType, $identifier)->willReturn($eventStream);
        $aggregateRootFactory->loadFromHistory($aggregateRootType, $eventStream)->willReturn($aggregateRoot);
        $unitOfWork->registerAggregate($aggregateRoot, $eventBus, $saveAggregateCallback)->willReturn($aggregateRoot);

        $this->load($identifier)->shouldReturn($aggregateRoot);
    }

    public function it_should_throw_an_exception_if_no_save_aggregate_callback_factory_has_been_set(
        AggregateRootIdentifier $identifier,
        $eventStore,
        $aggregateRootType,
        $aggregateRootFactory,
        EventStream $eventStream,
        AggregateRoot $aggregateRoot,
        $unitOfWork,
        $eventBus,
        $saveAggregateCallback
    ) {
        $eventStore->read($aggregateRootType, $identifier)->willReturn($eventStream);
        $aggregateRootFactory->loadFromHistory($aggregateRootType, $eventStream)->willReturn($aggregateRoot);
        $unitOfWork->registerAggregate($aggregateRoot, $eventBus, $saveAggregateCallback)->willReturn($aggregateRoot);

        $this->shouldThrow()->duringLoad($identifier);
    }

    public function it_should_throw_an_exception_if_the_loaded_aggregate_has_no_history(
        AggregateRootIdentifier $identifier,
        $aggregateRootType,
        $eventStore,
        EventStream $eventStream
    ) {
        $eventStore->read($aggregateRootType, $identifier)->willReturn($eventStream);
        $eventStream->isEmpty()->willReturn(true);

        $this->shouldThrow(new AggregateNotFoundException($identifier->getWrappedObject()))->during('load', [$identifier]);
    }

    public function it_can_expect_to_load_a_specific_version_of_the_aggregate_root(
        AggregateRootIdentifier $identifier,
        $eventStore,
        $aggregateRootType,
        $aggregateRootFactory,
        EventStream $eventStream,
        AggregateRoot $aggregateRoot,
        $saveAggregateCallbackFactory
    ) {
        $this->setSaveAggregateCallbackFactory($saveAggregateCallbackFactory);

        $eventStore->read($aggregateRootType, $identifier)->willReturn($eventStream);
        $aggregateRootFactory->loadFromHistory($aggregateRootType, $eventStream)->willReturn($aggregateRoot);

        $aggregateRoot->lastCommittedEventSequenceNumber()->willReturn(12);

        $this->load($identifier, 12);
        $this->shouldThrow(new ConflictingAggregateVersionException($identifier->getWrappedObject(), 12, 10))->during('load', [$identifier, 10]);
    }

    public function it_can_append_uncommitted_changes_to_the_event_store(AggregateRoot $aggregateRoot, $eventStore, $aggregateRootType, EventStream $eventStream)
    {
        $aggregateRoot->uncommittedChanges()->willReturn($eventStream);
        $eventStore->append($aggregateRootType, $eventStream)->shouldBeCalledTimes(1);

        $this->doSave($aggregateRoot);
    }

    public function it_should_not_solicitate_the_event_store_if_there_are_not_uncommitted_changes(AggregateRoot $aggregateRoot, $eventStore, $aggregateRootType, EventStream $eventStream)
    {
        $aggregateRoot->uncommittedChanges()->willReturn($eventStream);
        $eventStream->isEmpty()->willReturn(true);
        $eventStore->append(Argument::cetera())->shouldNotBeCalled();

        $this->doSave($aggregateRoot);
    }
}
