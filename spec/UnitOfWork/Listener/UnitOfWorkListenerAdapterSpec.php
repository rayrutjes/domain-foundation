<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\Listener;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class UnitOfWorkListenerAdapterSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('RayRutjes\DomainFoundation\Stub\UnitOfWork\Listener\UnitOfWorkListenerAdapterStub');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerAdapter');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener');
    }

    public function it_handles_after_commit(UnitOfWork $unitOfWork)
    {
        $this->afterCommit($unitOfWork);
    }

    public function it_handles_on_rollback(UnitOfWork $unitOfWork, \Exception $failureCause)
    {
        $this->onRollback($unitOfWork, $failureCause);
    }

    public function it_handles_on_event_registration(UnitOfWork $unitOfWork, Event $event)
    {
        $this->onEventRegistration($unitOfWork, $event)->shouldReturn($event);
    }

    public function it_handles_on_prepare_commit(UnitOfWork $unitOfWork)
    {
        $this->onPrepareCommit($unitOfWork, [], []);
    }

    public function it_handles_on_prepare_transaction_commit(UnitOfWork $unitOfWork)
    {
        $this->onPrepareTransactionCommit($unitOfWork);
    }

    public function it_handles_on_cleanup(UnitOfWork $unitOfWork)
    {
        $this->onCleanup($unitOfWork);
    }
}
