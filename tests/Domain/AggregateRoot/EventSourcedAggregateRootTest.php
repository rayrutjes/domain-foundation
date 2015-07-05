<?php

namespace RayRutjes\DomainFoundation\Test\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\EventSourcedAggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class EventSourcedAggregateRootTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EventSourcedAggregateRootStub
     */
    private $aggregateRoot;

    /**
     * @var AggregateRootIdentifierStub
     */
    private $identifier;

    public function setUp()
    {
        $this->identifier = new AggregateRootIdentifierStub('identifier');
        $this->aggregateRoot = EventSourcedAggregateRootStub::create($this->identifier);
    }

    public function testCanRetrieveItsIdentifier()
    {
        $this->assertTrue($this->identifier->equals($this->aggregateRoot->identifier()));
    }

    /**
     * @expectedException \LogicException
     */
    public function testIdentifierCanNotBeRetrievedIfNotSet()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::createWithoutIdentifier();
        $aggregateRoot->identifier();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testIdentifierCannotBeOverriddenOnceSet()
    {
        $this->aggregateRoot->overrideIdentifier();
    }

    public function testCanReturnTheUncommittedEventStream()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream', $this->aggregateRoot->uncommittedChanges());
    }

    public function testCanBeLoadedFromAnEventStream()
    {
        $identifier = new AggregateRootIdentifierStub('identifier');
        $event = new GenericEvent($identifier, 1, MessageIdentifier::generate(), new Created($identifier));

        $stream = new GenericEventStream([$event]);

        $aggregateRoot = EventSourcedAggregateRootStub::loadFromHistory($stream);

        return $aggregateRoot;
    }

    public function testCanReturnAnEmptyEventStreamIfThereAreNoPendingChanges()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::createWithoutIdentifier();
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream', $aggregateRoot->uncommittedChanges());
    }

    public function testLastCommittedSequenceNumberShouldBeZeroIfNoChangesHaveBeenCommitted()
    {
        $this->assertEquals(0, $this->aggregateRoot->lastCommittedEventSequenceNumber());
    }

    public function testLastCommittedSequenceNumberShouldReflectTheLastCommittedEventSequenceNumber()
    {
        $this->aggregateRoot->commitChanges();
        $this->assertEquals(1, $this->aggregateRoot->lastCommittedEventSequenceNumber());
    }

    /**
     * @depends testCanBeLoadedFromAnEventStream
     */
    public function testLastCommittedSequenceNumberShouldReflectTheLastCommittedEventSequenceNumberWhenAggregateIsLoadedFromHistory(EventSourcedAggregateRootStub $aggregateRoot)
    {
        $this->assertEquals(1, $aggregateRoot->lastCommittedEventSequenceNumber());
    }

    public function testCanAttachAnEventRegistrationCallbackToTheEventContainer()
    {
        $callback = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback')->getMock();

        // This will be tested more in depth in the integration suite.
        $this->aggregateRoot->addEventRegistrationCallback($callback);
    }

    public function testItCanTellIfItHasTheSameIdentityAsAnotherAggregateRoot()
    {
        $aggregateRoot2 = EventSourcedAggregateRootStub::createWithIdentifier(new AggregateRootIdentifierStub('identifier'));
        $this->assertTrue($this->aggregateRoot->sameIdentityAs($aggregateRoot2));
    }

    public function testItCanTellIfItHasNotTheSameIdentityAsAnotherAggregateRoot()
    {
        $aggregateRoot2 = EventSourcedAggregateRootStub::createWithIdentifier(new AggregateRootIdentifierStub('another_identifier'));
        $this->assertFalse($this->aggregateRoot->sameIdentityAs($aggregateRoot2));
    }

    public function testShouldNotConsiderAggregateRootsTheSameIfTheImplementationsDiffer()
    {
        $aggregateRoot2 = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\AggregateRoot\EventSourcedAggregateRoot')
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertFalse($this->aggregateRoot->sameIdentityAs($aggregateRoot2));
    }
}

class EventSourcedAggregateRootStub extends EventSourcedAggregateRoot
{
    public static function createWithoutIdentifier()
    {
        return new self();
    }

    public static function createWithIdentifier(AggregateRootIdentifier $identifier)
    {
        $instance = new self();
        $instance->setIdentifier($identifier);

        return $instance;
    }

    public static function create(AggregateRootIdentifierStub $identifier)
    {
        $instance = new self();
        $instance->applyChange(new Created($identifier));

        return $instance;
    }

    public function overrideIdentifier()
    {
        $this->setIdentifier(new AggregateRootIdentifierStub('identifier'));
    }

    protected function applyCreated(Created $created)
    {
        $this->setIdentifier($created->identifier());
    }
}

class AggregateRootIdentifierStub implements AggregateRootIdentifier
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param string $uid
     */
    public function __construct($uid)
    {
        $this->identifier = (string) $uid;
    }
    /**
     * @return string
     */
    public function toString()
    {
        return $this->identifier;
    }

    /**
     * @param AggregateRootIdentifier $identifier
     *
     * @return mixed
     */
    public function equals(AggregateRootIdentifier $identifier)
    {
        if (!$identifier instanceof self) {
            return false;
        }

        return $this->identifier === $identifier->toString();
    }
}

class Created implements Serializable
{
    /**
     * @var AggregateRootIdentifierStub
     */
    private $identifier;

    /**
     * @param AggregateRootIdentifierStub $identifier
     */
    public function __construct(AggregateRootIdentifierStub $identifier)
    {
        $this->identifier = $identifier->toString();
    }

    /**
     * @return AggregateRootIdentifierStub
     */
    public function identifier()
    {
        return new AggregateRootIdentifierStub($this->identifier);
    }
}
