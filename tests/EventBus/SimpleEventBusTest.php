<?php

namespace RayRutjes\DomainFoundation\Test\EventBus;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\SimpleEventBus;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class SimpleEventBusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleEventBus
     */
    private $eventBus;

    public function setUp()
    {
        $this->eventBus = new SimpleEventBus();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresOnlyEventsCanBePublished()
    {
        $this->eventBus->publish([new \stdClass()]);
    }

    public function testDelegatesHandlingOfPublishedEventsToSubscribedListeners()
    {
        $event1 = new EventStub();
        $event2 = new EventStub();

        $listener1 = $this->getMockBuilder('RayRutjes\DomainFoundation\EventBus\EventListener')->getMock();
        $listener1->expects($this->exactly(2))
                  ->method('handle')
                  ->withConsecutive($this->identicalTo($event1), $this->identicalTo($event2))
        ;

        $listener2 = $this->getMockBuilder('RayRutjes\DomainFoundation\EventBus\EventListener')->getMock();
        $listener2->expects($this->exactly(2))
            ->method('handle')
            ->withConsecutive($this->identicalTo($event1), $this->identicalTo($event2))
        ;

        $this->eventBus->subscribe($listener1);
        $this->eventBus->subscribe($listener2);

        $this->eventBus->publish([$event1, $event2]);
    }

    public function testShouldNotDelegatesHandlingOfPublishedEventsToUnsubscribedListeners()
    {
        $event1 = new EventStub();
        $event2 = new EventStub();

        $listener1 = $this->getMockBuilder('RayRutjes\DomainFoundation\EventBus\EventListener')->getMock();
        $listener1->expects($this->exactly(2))
                  ->method('handle')
                  ->withConsecutive($this->identicalTo($event1), $this->identicalTo($event2))
        ;

        $listener2 = $this->getMockBuilder('RayRutjes\DomainFoundation\EventBus\EventListener')->getMock();
        $listener2->expects($this->never())
                  ->method('handle');

        $this->eventBus->subscribe($listener1);
        $this->eventBus->subscribe($listener2);

        $this->eventBus->unsubscribe($listener2);

        $this->eventBus->publish([$event1, $event2]);
    }
}

class EventStub implements Event
{
    /**
     * @return AggregateRootIdentifier
     */
    public function aggregateRootIdentifier()
    {
    }

    /**
     * @return int
     */
    public function sequenceNumber()
    {
    }

    /**
     * @return MessageIdentifier
     */
    public function identifier()
    {
    }

    /**
     * @return Serializable
     */
    public function payload()
    {
    }

    /**
     * @return Contract
     */
    public function payloadType()
    {
    }

    /**
     * @return Metadata
     */
    public function metadata()
    {
    }

    /**
     * @return Contract
     */
    public function metadataType()
    {
    }
}
