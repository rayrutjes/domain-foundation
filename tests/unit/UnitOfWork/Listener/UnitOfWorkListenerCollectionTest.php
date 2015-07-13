<?php

namespace RayRutjes\DomainFoundation\Test\Unit\UnitOfWork\Listener;

use RayRutjes\DomainFoundation\Test\Unit\Domain\Event\Stream\EventStub;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection;

class UnitOfWorkListenerCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UnitOfWorkListenerCollection
     */
    private $listeners;

    private $listener1;
    private $listener2;

    public function setUp()
    {
        $this->listener1 = $this->listenerMock();
        $this->listener2 = $this->listenerMock();

        $this->listeners = new UnitOfWorkListenerCollection();
        $this->listeners->add($this->listener1);
        $this->listeners->add($this->listener2);
    }

    public function testCanTriggerTheAfterCommitOfAllListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();

        // Todo: We should test that the listeners are triggered in the reversed order of registering.
        $this->listener1->expects($this->once())
            ->method('afterCommit')
            ->with($this->identicalTo($unitOfWork));

        $this->listener2->expects($this->once())
            ->method('afterCommit')
            ->with($this->identicalTo($unitOfWork));

        $this->listeners->afterCommit($unitOfWork);
    }

    public function testCanNotifyRollbackToListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();

        $failureCause = new \Exception();

        // Todo: We should test that the listeners are triggered in the reversed order of registering.
        $this->listener1->expects($this->once())
            ->method('onRollback')
            ->with($this->identicalTo($unitOfWork), $this->identicalTo($failureCause));

        $this->listener2->expects($this->once())
            ->method('onRollback')
            ->with($this->identicalTo($unitOfWork), $this->identicalTo($failureCause));

        $this->listeners->onRollback($unitOfWork, $failureCause);
    }

    public function testCanNotifyEventRegistrationToListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();
        $event = new EventStub(0);

        $this->listener1->expects($this->once())
            ->method('onEventRegistration')
            ->with($this->identicalTo($unitOfWork), $this->identicalTo($event))
            ->will($this->returnArgument(1))
        ;

        $this->listener2->expects($this->once())
            ->method('onEventRegistration')
            ->with($this->identicalTo($unitOfWork), $this->identicalTo($event))
            ->will($this->returnArgument(1))
        ;

        $this->assertSame(
            $event,
            $this->listeners->onEventRegistration($unitOfWork, $event)
        );
    }

    public function testCanNotifyCommitPreparationToListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();

        $this->listener1->expects($this->once())
            ->method('onPrepareCommit')
            ->with($this->identicalTo($unitOfWork), [], []);

        $this->listener2->expects($this->once())
            ->method('onPrepareCommit')
            ->with($this->identicalTo($unitOfWork), [], []);

        $this->listeners->onPrepareCommit($unitOfWork, [], []);
    }

    public function testCanNotifyTransactionCommitPreparationToListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();

        $this->listener1->expects($this->once())
            ->method('onPrepareTransactionCommit')
            ->with($this->identicalTo($unitOfWork));

        $this->listener2->expects($this->once())
            ->method('onPrepareTransactionCommit')
            ->with($this->identicalTo($unitOfWork));

        $this->listeners->onPrepareTransactionCommit($unitOfWork);
    }

    public function testCanNotifyCleanupToListeners()
    {
        $unitOfWork = $this->unitOfWorkMock();

        // Todo: We should test that the listeners are triggered in the reversed order of registering.
        $this->listener1->expects($this->once())
            ->method('onCleanup')
            ->with($this->identicalTo($unitOfWork));

        $this->listener2->expects($this->once())
            ->method('onCleanup')
            ->with($this->identicalTo($unitOfWork));

        $this->listeners->onCleanup($unitOfWork);
    }



    public function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }

    private function listenerMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener');
    }
}
