<?php

namespace RayRutjes\DomainFoundation\Test\Unit\UnitOfWork;

use RayRutjes\DomainFoundation\EventBus\SimpleEventBus;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\EventSourcedAggregateRootStub;
use RayRutjes\DomainFoundation\Test\Unit\Domain\Event\Stream\EventStub;
use RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWork;

class DefaultUnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultUnitOfWork
     */
    private $unitOfWork;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionManager;

    private $registeredAggregates;

    private $listeners;

    private $stagingEvents;

    public function setUp()
    {
        $this->transactionManager = $this->getMock(\RayRutjes\DomainFoundation\UnitOfWork\TransactionManager::class);
        $this->unitOfWork = new DefaultUnitOfWork($this->transactionManager);

        $reflection = new \ReflectionClass($this->unitOfWork);
        $this->injectAggregateRootContainerMock($reflection);
        $this->injectListenerCollectionMock($reflection);
        $this->injectStagingEventContainerMock($reflection);
    }

    private function injectAggregateRootContainerMock(\ReflectionClass $reflection)
    {
        $this->registeredAggregates = $this->getMockBuilder(
            'RayRutjes\DomainFoundation\UnitOfWork\AggregateRootContainer'
        )
            ->disableOriginalConstructor()
            ->getMock();
        $this->registeredAggregates->method('all')->willReturn([]);
        $this->registeredAggregates->method('add')->will($this->returnArgument(0));

        $property = $reflection->getProperty('registeredAggregates');
        $property->setAccessible(true);
        $property->setValue($this->unitOfWork, $this->registeredAggregates);
    }

    private function injectListenerCollectionMock(\ReflectionClass $reflection)
    {
        $this->listeners = $this->getMock(
            'RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection'
        );

        $this->listeners->method('onEventRegistration')->will($this->returnArgument(1));

        $property = $reflection->getProperty('listeners');
        $property->setAccessible(true);
        $property->setValue($this->unitOfWork, $this->listeners);
    }

    private function injectStagingEventContainerMock(\ReflectionClass $reflection)
    {
        $this->stagingEvents = $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer');
        $this->stagingEvents->method('all')->willReturn([]);

        $property = $reflection->getProperty('stagingEvents');
        $property->setAccessible(true);
        $property->setValue($this->unitOfWork, $this->stagingEvents);
    }

    public function testCanBeStarted()
    {
        $this->unitOfWork->start();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCannotBeStartedIfItHasAlreadyStarted()
    {
        $this->unitOfWork->start();
        $this->unitOfWork->start();
    }

    public function testShouldStartATransactionIfATransactionManagerIsAvailable()
    {
        $this->transactionManager->expects($this->once())->method('startTransaction');

        $this->unitOfWork->start();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCanNotBeCommittedIfNotStarted()
    {
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedThatTheCommitIsInPreparation()
    {
        $this->listeners->expects($this->once())
            ->method('onPrepareCommit')
            ->with(
                $this->identicalTo($this->unitOfWork),
                $this->equalTo([]),
                $this->equalTo([])
            );

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testRegisteredAggregateRootsShouldBeSavedOnCommit()
    {
        $this->registeredAggregates->expects($this->once())->method('saveAggregateRoots');

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testStagingEventsShouldBePublishedOnCommit()
    {
        $this->stagingEvents->expects($this->once())->method('publishEvents');

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedThatTheTransactionIsBeingRolledBack()
    {
        $this->listeners->expects($this->once())
            ->method('onPrepareTransactionCommit')
            ->with($this->unitOfWork);

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedThatTheTransactionIsAboutToBeCommitted()
    {
        $this->listeners->expects($this->once())
            ->method('onPrepareTransactionCommit')
            ->with($this->unitOfWork);

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testTransactionShouldBeCommittedOnCommit()
    {
        $this->transactionManager->expects($this->once())->method('commitTransaction');
        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedThatTheCommitSucceeded()
    {
        $this->listeners->expects($this->once())
            ->method('afterCommit')
            ->with($this->unitOfWork);

        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedThatTheUnitOfWorkWasStopped()
    {
        $this->listeners->expects($this->once())
            ->method('onCleanup')
            ->with($this->unitOfWork);
        $this->unitOfWork->start();
        $this->unitOfWork->commit();
    }

    public function testUnitOfWorkCanBeRestartedOnceCommitHasEnded()
    {
        $this->unitOfWork->start();
        $this->unitOfWork->commit();
        $this->unitOfWork->start();
    }

    private function setUpRollbackContext($failureCause = null)
    {
        $failureCause = null === $failureCause ? new \RuntimeException() : $failureCause;
        $this->listeners->method('onPrepareCommit')
            ->will($this->throwException($failureCause));

        $this->unitOfWork->start();
    }

    public function testShouldClearRegisteredAggregatesOnRollback()
    {
        $this->setUpRollbackContext();
        $this->registeredAggregates->expects($this->once())->method('clear');
        $this->unitOfWork->commit();
    }

    public function testShouldClearStagingEventsOnRollback()
    {
        $this->setUpRollbackContext();
        $this->stagingEvents->expects($this->once())->method('clear');
        $this->unitOfWork->commit();
    }

    public function testShouldRollbackTransactionOnRollback()
    {
        $this->setUpRollbackContext();
        $this->transactionManager->expects($this->once())->method('rollbackTransaction');
        $this->unitOfWork->commit();
    }

    public function testListenersShouldBeNotifiedOfTheRollback()
    {
        $failureCause = new \RuntimeException();
        $this->setUpRollbackContext($failureCause);
        $this->listeners->expects($this->once())
            ->method('onRollback')
            ->with(
                $this->identicalTo($this->unitOfWork),
                $this->identicalTo($failureCause)
            );

        $this->unitOfWork->commit();
    }

    public function testItCanRegisterAnAggregateRoot()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::createWithoutIdentifier();
        $eventBus = new SimpleEventBus();
        $callback = $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback');

        $this->registeredAggregates->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($aggregateRoot), $this->identicalTo($eventBus, $this->identicalTo($callback)));

        $this->assertSame(
            $aggregateRoot,
            $this->unitOfWork->registerAggregate($aggregateRoot, $eventBus, $callback)
        );
    }

    public function testItCanInvokeTheEventRegistrationListeners()
    {
        $event = new EventStub(0);

        $this->listeners->expects($this->once())
            ->method('onEventRegistration')
            ->with($this->identicalTo($this->unitOfWork), $this->identicalTo($event));

        $this->assertSame(
            $event,
            $this->unitOfWork->invokeEventRegistrationListeners($event)
        );
    }

    public function testItCanStageAnEventForPublication()
    {
        $event = new EventStub(0);
        $eventBus = new SimpleEventBus();

        $this->stagingEvents->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($event), $this->identicalTo($eventBus));

        $this->unitOfWork->publishEvent($event, $eventBus);
    }

    public function testItCanRegisterAListener()
    {
        $listener = $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListener');

        $this->listeners->expects($this->once())
            ->method('add')
            ->with($this->identicalTo($listener));

        $this->unitOfWork->registerListener($listener);
    }
}
