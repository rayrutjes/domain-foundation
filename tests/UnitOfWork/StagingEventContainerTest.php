<?php

namespace RayRutjes\DomainFoundation\Test\UnitOfWork;

use RayRutjes\DomainFoundation\Test\Domain\Event\Stream\EventStub;
use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer;

class StagingEventContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StagingEventContainer
     */
    private $container;

    public function setUp()
    {
        $this->container = new StagingEventContainer();
    }

    public function testItCanRegisterAnEvent()
    {
        $event = $this->event();
        $this->container->add($event, $this->eventBusMock());
        $this->assertSame([$event], $this->container->all());
    }

    public function testCanReturnAllRegisteredEvents()
    {
        $event1 = $this->event();
        $event2 = $this->event();
        $eventBus = $this->eventBusMock();
        $this->container->add($event1, $eventBus);
        $this->container->add($event2, $eventBus);

        $this->assertSame([$event1, $event2], $this->container->all());
    }

    public function testCanClearAllRegisteredEvents()
    {
        $event1 = $this->event();
        $event2 = $this->event();
        $eventBus = $this->eventBusMock();
        $this->container->add($event1, $eventBus);
        $this->container->add($event2, $eventBus);

        $this->container->clear();

        $this->assertEmpty($this->container->all());
    }

    public function testCanPublishEachEventThroughTheEventBusItWasRegisteredWith()
    {
        $event1 = $this->event();
        $event2 = $this->event();
        $event3 = $this->event();
        $eventBus1 = $this->eventBusMock();
        $eventBus1->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo([$event1]));

        $eventBus2 = $this->eventBusMock();
        $eventBus2->expects($this->once())
            ->method('publish')
            ->with($this->identicalTo([$event2, $event3]));

        $this->container->add($event1, $eventBus1);
        $this->container->add($event2, $eventBus2);
        $this->container->add($event3, $eventBus2);

        $this->container->publishEvents();

        return $this->container;
    }

    /**
     * @depends testCanPublishEachEventThroughTheEventBusItWasRegisteredWith
     */
    public function testShouldBeClearedAfterEventsWerePublished(StagingEventContainer $container)
    {
        $this->assertEmpty($container->all());
    }


    private function event()
    {
        return new EventStub(0);
    }

    private function eventBusMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\EventBus\EventBus');
    }
}
