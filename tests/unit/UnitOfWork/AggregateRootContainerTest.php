<?php

namespace RayRutjes\DomainFoundation\Test\Unit\UnitOfWork;

use RayRutjes\DomainFoundation\EventBus\SimpleEventBus;
use RayRutjes\DomainFoundation\UnitOfWork\AggregateRootContainer;

class AggregateRootContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateRootContainer
     */
    private $container;

    private $unitOfWork;

    public function setUp()
    {
        $this->unitOfWork = $this->unitOfWorkMock();
        $this->container = new AggregateRootContainer($this->unitOfWork);
    }

    public function testItShouldAttachAnEventRegistrationCallbackToAddedAggregateRoot()
    {
        $aggregateRoot1 = $this->aggregateRootMock();
        $aggregateRoot1->expects($this->once())
            ->method('addEventRegistrationCallback')
            ->with(
                $this->isInstanceOf('RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWorkEventRegistrationCallback')
            );

        $eventBus = $this->eventBus();
        $saveCallback = $this->saveAggregateCallbackMock();

        $this->assertSame($aggregateRoot1, $this->container->add($aggregateRoot1, $eventBus, $saveCallback));
    }

    public function testItShouldReturnAPreviouslyRegisteredAggregateRootIfItIsSimilarToTheOneBeingAdded()
    {
        // Todo: also tests that nothing else is done (registering aggregate root, registering save callback, adding callback to aggregate root)
        $aggregateRoot1 = $this->aggregateRootMock();
        $aggregateRoot1->method('sameIdentityAs')->willReturn(true);

        $aggregateRoot2 = $this->aggregateRootMock();

        $eventBus = $this->eventBus();
        $saveCallback = $this->saveAggregateCallbackMock();

        $this->container->add($aggregateRoot1, $eventBus, $saveCallback);

        $this->assertSame($aggregateRoot1, $this->container->add($aggregateRoot2, $eventBus, $saveCallback));
    }

    public function testItCanReturnAllRegisteredAggregateRoots()
    {
        // Todo: also tests that nothing else is done (registering aggregate root, registering save callback, adding callback to aggregate root)
        $aggregateRoot1 = $this->aggregateRootMock();
        $aggregateRoot2 = $this->aggregateRootMock();
        $eventBus = $this->eventBus();
        $saveCallback = $this->saveAggregateCallbackMock();

        $this->container->add($aggregateRoot1, $eventBus, $saveCallback);
        $this->container->add($aggregateRoot2, $eventBus, $saveCallback);

        $this->assertSame([
            $aggregateRoot1,
            $aggregateRoot2
        ], $this->container->all());
    }

    public function testItCanTriggerAllSaveCallbacksOnTheirRespectiveAggregateRoot()
    {
        $aggregateRoot1 = $this->aggregateRootMock();
        $aggregateRoot2 = $this->aggregateRootMock();
        $eventBus = $this->eventBus();

        $saveCallback1 = $this->saveAggregateCallbackMock();
        $saveCallback1->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($aggregateRoot1));

        $saveCallback2 = $this->saveAggregateCallbackMock();
        $saveCallback2->expects($this->once())
            ->method('save')
            ->with($this->identicalTo($aggregateRoot2));

        $this->container->add($aggregateRoot1, $eventBus, $saveCallback1);
        $this->container->add($aggregateRoot2, $eventBus, $saveCallback2);

        $this->container->saveAggregateRoots();
    }

    public function testItCanClearAllRegisteredAggregateRoots()
    {
        $aggregateRoot1 = $this->aggregateRootMock();
        $aggregateRoot2 = $this->aggregateRootMock();
        $eventBus = $this->eventBus();

        $saveCallback = $this->saveAggregateCallbackMock();
        $this->container->add($aggregateRoot1, $eventBus, $saveCallback);
        $this->container->add($aggregateRoot2, $eventBus, $saveCallback);

        $this->container->clear();
        $this->assertSame([], $this->container->all());
    }



    private function eventBus()
    {
        return new SimpleEventBus();
    }

    private function saveAggregateCallbackMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback');
    }

    private function aggregateRootMock()
    {
        return $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot')
            ->getMock();
    }

    private function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }
}
