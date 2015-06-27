<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\Listener;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class UnitOfWorkListenerCollectionSpec extends ObjectBehavior
{
    public function let(UnitOfWorkListener $listener1, UnitOfWorkListener $listener2)
    {
        $this->add($listener1);
        $this->add($listener2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener');
    }

    public function it_can_invoke_after_commit_callback_on_registered_listeners(UnitOfWork $unitOfWork, $listener1, $listener2)
    {
        $listener1->afterCommit($unitOfWork)->shouldBeCalledTimes(1);
        $listener2->afterCommit($unitOfWork)->shouldBeCalledTimes(1);

        $this->afterCommit($unitOfWork);
    }

    public function it_can_invoke_on_rollback_callback_on_registered_listeners(UnitOfWork $unitOfWork, \Exception $failureCause, $listener1, $listener2)
    {
        $listener1->onRollback($unitOfWork, $failureCause)->shouldBeCalledTimes(1);
        $listener2->onRollback($unitOfWork, $failureCause)->shouldBeCalledTimes(1);

        $this->onRollback($unitOfWork, $failureCause);
    }

    public function it_can_invoke_on_event_registration_callback_on_registered_listeners(UnitOfWork $unitOfWork, Event $event, $listener1, $listener2)
    {
        $listener1->onEventRegistration($unitOfWork, $event)->shouldBeCalledTimes(1)->willReturn($event);
        $listener2->onEventRegistration($unitOfWork, $event)->shouldBeCalledTimes(1)->willReturn($event);

        $this->onEventRegistration($unitOfWork, $event)->shouldReturn($event);
    }

    public function it_can_invoke_on_prepare_commit_on_registered_listeners(UnitOfWork $unitOfWork, $listener1, $listener2)
    {
        $listener1->onPrepareCommit($unitOfWork, [], [])->shouldBeCalledTimes(1);
        $listener2->onPrepareCommit($unitOfWork, [], [])->shouldBeCalledTimes(1);

        $this->onPrepareCommit($unitOfWork, [], []);
    }

    public function it_can_invoke_on_prepare_transaction_commit_on_registered_listeners(UnitOfWork $unitOfWork, $listener1, $listener2)
    {
        $listener1->onPrepareTransactionCommit($unitOfWork)->shouldBeCalledTimes(1);
        $listener2->onPrepareTransactionCommit($unitOfWork)->shouldBeCalledTimes(1);

        $this->onPrepareTransactionCommit($unitOfWork);
    }

    public function it_can_invoke_on_cleanup_on_registered_listeners(UnitOfWork $unitOfWork, $listener1, $listener2)
    {
        $listener1->onCleanup($unitOfWork)->shouldBeCalledTimes(1);
        $listener2->onCleanup($unitOfWork)->shouldBeCalledTimes(1);

        $this->onCleanup($unitOfWork);
    }
}
