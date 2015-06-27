<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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
use RayRutjes\DomainFoundation\UnitOfWork\TransactionManager;

class DefaultUnitOfWorkSpec extends ObjectBehavior
{
    public function let(
        UnitOfWorkListenerCollection $listeners,
        StagingEventContainerFactory $stagingEventContainerFactory,
        AggregateContainerFactory $aggregateContainerFactory,
        UnitOfWorkEventRegistrationCallbackFactory $eventRegistrationCallbackFactory,
        TransactionManager $transactionManager,
        StagingEventContainer $stagingEventContainer,
        AggregateContainer $aggregateContainer
    ) {
        $stagingEventContainerFactory->create()->willReturn($stagingEventContainer);
        $aggregateContainerFactory->create(Argument::any(), $eventRegistrationCallbackFactory)->willReturn($aggregateContainer);

        $this->beConstructedWith(
            $listeners,
            $stagingEventContainerFactory,
            $aggregateContainerFactory,
            $eventRegistrationCallbackFactory,
            $transactionManager
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWork');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }

    public function it_should_start_the_transaction_when_it_is_started($transactionManager)
    {
        $transactionManager->startTransaction()->shouldBeCalled();
        $this->start();
    }

    public function it_should_not_allow_going_through_the_starting_process_if_it_has_already_been_started()
    {
        $this->start();
        $this->shouldThrow(new \Exception('The unit of work has already been started.'))->during('start');
    }

    public function it_can_be_restarted_after_it_has_been_stopped()
    {
        $this->start();
        $this->stop();
        $this->start();
    }

    public function it_should_not_allow_being_committed_if_it_has_not_been_started()
    {
        $this->shouldThrow(new \Exception('The unit of work has not been started.'))->during('commit');
    }

    public function it_can_register_an_aggregate(
        AggregateRoot $aggregateRoot,
        EventBus $eventBus,
        SaveAggregateCallback $saveAggregateCallback,
        $aggregateContainer
    ) {
        $aggregateContainer->add($aggregateRoot, $eventBus, $saveAggregateCallback)->shouldBeCalled()->willReturn($aggregateRoot);
        $this->registerAggregate($aggregateRoot, $eventBus, $saveAggregateCallback)->shouldReturn($aggregateRoot);
    }

    public function it_can_invoke_event_registration_listeners_for_a_given_event($listeners, Event $event)
    {
        $listeners->onEventRegistration($this, $event)->shouldBeCalled();
        $this->invokeEventRegistrationListeners($event);
    }

    public function it_can_register_a_new_listener($listeners, UnitOfWorkListener $listener)
    {
        $listeners->add($listener)->shouldBeCalled();
        $this->registerListener($listener);
    }

    public function it_should_stage_events_for_publishing($stagingEventContainer, Event $event, EventBus $eventBus)
    {
        $stagingEventContainer->add($event, $eventBus)->shouldBeCalled();
        $stagingEventContainer->publishEvents()->shouldNotBeCalled();
        $this->publishEvent($event, $eventBus);
    }

    public function it_should_commit_the_transaction_and_publish_the_events_when_committed(
        $listeners,
        $stagingEventContainer,
        Event $event,
        $transactionManager,
        $aggregateContainer,
        AggregateRoot $aggregateRoot
    ) {
        $transactionManager->startTransaction()->shouldBeCalled();

        $aggregateContainer->all()->willReturn([$aggregateRoot]);
        $stagingEventContainer->all()->willReturn([$event]);

        // Listeners should be notified that this is about to be committed.
        $listeners->onPrepareCommit($this, [$aggregateRoot], [$event])->shouldBeCalled();

        $aggregateContainer->saveAggregateRoots()->shouldBeCalled();

        $stagingEventContainer->publishEvents()->shouldBeCalled();

        // Listeners should be notified that the transaction is about to be committed.
        $listeners->onPrepareTransactionCommit($this)->shouldBeCalled();

        $transactionManager->commitTransaction()->shouldBeCalled();

        // Listeners should be notified that the transaction was committed.
        $listeners->afterCommit($this)->shouldBeCalled();

        // Listeners should always be notified that this is being cleaned up.
        $listeners->onCleanup($this)->shouldBeCalled();

        $this->start();
        $this->commit();
    }

    public function it_should_rollback_if_an_exception_is_thrown_during_the_commit_process(
        $listeners,
        $stagingEventContainer,
        Event $event,
        $transactionManager,
        $aggregateContainer,
        AggregateRoot $aggregateRoot
    ) {
        $transactionManager->startTransaction()->shouldBeCalled();

        $aggregateContainer->all()->willReturn([$aggregateRoot]);
        $stagingEventContainer->all()->willReturn([$event]);

        // We fake an exception at this point.
        $failureCause = new \Exception();
        $listeners->onPrepareCommit($this, [$aggregateRoot], [$event])->willThrow($failureCause);

        $aggregateContainer->clear()->shouldBeCalled();
        $stagingEventContainer->clear()->shouldBeCalled();
        $transactionManager->rollbackTransaction()->shouldBeCalled();

        $listeners->onRollback($this, $failureCause)->shouldBeCalled();

        $aggregateContainer->saveAggregateRoots()->shouldNotBeCalled();

        $stagingEventContainer->publishEvents()->shouldNotBeCalled();

        // Listeners should be notified that the transaction is about to be committed.
        $listeners->onPrepareTransactionCommit(Argument::any())->shouldNotBeCalled();

        $transactionManager->commitTransaction()->shouldNotBeCalled();

        // Listeners should be notified that the transaction was committed.
        $listeners->afterCommit(Argument::any())->shouldNotBeCalled();

        // Listeners should always be notified that this is being cleaned up.
        $listeners->onCleanup($this)->shouldBeCalled();

        $this->start();
        $this->shouldThrow($failureCause)->during('commit');
    }
}
