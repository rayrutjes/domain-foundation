<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Domain\Event\Container;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Container\DefaultEventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Message\Message;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultEventContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultEventContainer
     */
    private $eventContainer;

    private $aggregateRootIdentifier;

    private $lastCommittedSequenceNumber;

    public function setUp()
    {
        $this->aggregateRootIdentifier = new AggregateRootIdentifierStub();
        $this->lastCommittedSequenceNumber = 3;

        $this->eventContainer = new DefaultEventContainer($this->aggregateRootIdentifier, $this->lastCommittedSequenceNumber);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheSequenceNumberIsAnInteger()
    {
        new DefaultEventContainer($this->aggregateRootIdentifier, '3');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheSequenceNumberIsPositive()
    {
        new DefaultEventContainer($this->aggregateRootIdentifier, -1);
    }

    public function testAnEventCanBeAdded()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
        $this->assertCount(1, $this->eventContainer);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testEnsuresSequenceIsNotCorrupted()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 99));
    }

    public function testAnEventCanByAddedByOnlyProvidingThePayload()
    {
        $this->eventContainer->addEventFromPayload(new PayloadStub());
        $this->assertCount(1, $this->eventContainer);
    }

    public function testProvidesAStreamOfAllEvents()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream', $this->eventContainer->eventStream());
    }

    public function testCanRetrieveTheLastCommittedSequenceNumber()
    {
        $this->assertEquals($this->lastCommittedSequenceNumber, $this->eventContainer->lastCommittedSequenceNumber());
    }

    public function testLastSequenceNumberEqualsTheLastCommittedSequenceNumberIfThereAreNoEvents()
    {
        $this->assertEquals($this->lastCommittedSequenceNumber, $this->eventContainer->lastSequenceNumber());
    }

    public function testLastSequenceNumberEqualsTheLastRegisteredEventSequenceNumber()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 5));
        $this->assertEquals(5, $this->eventContainer->lastSequenceNumber());
    }

    public function testProvideTheNumberOfEventsInTheContainer()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
        $this->assertCount(1, $this->eventContainer);
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 5));
        $this->assertCount(2, $this->eventContainer);
    }

    public function testNewlyAddedRegistrationCallbackShouldBeAppliedToExistingEventsInTheContainer()
    {
        $event1 = new EventStub($this->aggregateRootIdentifier, 4);
        $this->eventContainer->addEvent($event1);

        $event2 = new EventStub($this->aggregateRootIdentifier, 5);
        $this->eventContainer->addEvent($event2);

        $callback = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback')->getMock();

        $callback->expects($this->exactly(2))
                 ->method('onEventRegistration')
                 ->withConsecutive(
                     $this->identicalTo($event1),
                     $this->identicalTo($event2)
                 );

        $this->eventContainer->addRegistrationCallback($callback);
    }

    public function testRegsitrationCallbacksAreAppliedToNewlyAddedEvents()
    {
        $event1 = new EventStub($this->aggregateRootIdentifier, 4);
        $event2 = new EventStub($this->aggregateRootIdentifier, 5);

        $callback1 = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback')->getMock();
        $callback1->expects($this->exactly(2))
                  ->method('onEventRegistration')
                  ->withConsecutive(
                      $this->identicalTo($event1),
                      $this->identicalTo($event2)
                  )
                  ->will($this->returnArgument(0))
        ;

        $callback2 = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback')->getMock();
        $callback2->expects($this->exactly(2))
                  ->method('onEventRegistration')
                  ->withConsecutive(
                      $this->identicalTo($event1),
                      $this->identicalTo($event2)
                  )
                  ->will($this->returnArgument(0))
        ;

        $this->eventContainer->addRegistrationCallback($callback1);
        $this->eventContainer->addRegistrationCallback($callback2);

        $this->eventContainer->addEvent($event1);
        $this->eventContainer->addEvent($event2);
    }

    public function testShouldConsiderTheLastSequenceNumberAsTheLastCommittedSequenceNumberWhenCommitted()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
        $this->eventContainer->commit();
        $this->assertEquals(4, $this->eventContainer->lastCommittedSequenceNumber());
    }

    public function testShouldClearTheEventsWhenCommitted()
    {
        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
        $this->eventContainer->commit();
        $this->assertCount(0, $this->eventContainer);
    }

    public function testShouldNotClearTheRegistrationCallbacksWhenCommitted()
    {
        $callback = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback')->getMock();
        $callback->expects($this->once())
                 ->method('onEventRegistration');

        $this->eventContainer->addRegistrationCallback($callback);

        $this->eventContainer->commit();

        $this->eventContainer->addEvent(new EventStub($this->aggregateRootIdentifier, 4));
    }
}

class AggregateRootIdentifierStub implements AggregateRootIdentifier
{
    /**
     * @return string
     */
    public function toString()
    {
    }

    /**
     * @param AggregateRootIdentifier $identifier
     *
     * @return mixed
     */
    public function equals(AggregateRootIdentifier $identifier)
    {
    }
}

class PayloadStub implements Serializable
{
}

class EventStub implements Event
{
    /**
     * @var AggregateRootIdentifier
     */
    private $aggregateRootIdentifier;

    /**
     * @var int
     */
    private $sequenceNumber;

    public function __construct(AggregateRootIdentifier $aggregateRootIdentifier, $sequenceNumber)
    {
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;
        $this->sequenceNumber = $sequenceNumber;
    }

    /**
     * @return AggregateRootIdentifier
     */
    public function aggregateRootIdentifier()
    {
        return $this->aggregateRootIdentifier;
    }

    /**
     * @return int
     */
    public function sequenceNumber()
    {
        return $this->sequenceNumber;
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

    /**
     * @param array $data
     *
     * @return Message
     */
    public function enrichMetadata(array $data)
    {
    }
}
