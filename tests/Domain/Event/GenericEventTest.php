<?php

namespace RayRutjes\DomainFoundation\Test\Domain\Event;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericEvent
     */
    private $event;

    private $aggregateRootIdentifier;

    private $identifier;

    private $payload;

    private $metadata;

    public function setUp()
    {
        $this->aggregateRootIdentifier = new AggregateRootIdentifierStub();
        $this->identifier = MessageIdentifier::generate();
        $this->payload = new PayloadStub();
        $this->metadata = new Metadata();

        $this->event = new GenericEvent($this->aggregateRootIdentifier, 1, $this->identifier, $this->payload, $this->metadata);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresSequenceNumberIsAnInteger()
    {
        new GenericEvent($this->aggregateRootIdentifier, '3', $this->identifier, $this->payload, $this->metadata);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresSequenceNumberIsGreaterThanZero()
    {
        new GenericEvent($this->aggregateRootIdentifier, 0, $this->identifier, $this->payload, $this->metadata);
    }

    public function testImplementsEventInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Event', $this->event);
    }

    public function testCanRetrieveItsAggregateRootIdentifier()
    {
        $this->assertSame($this->aggregateRootIdentifier, $this->event->aggregateRootIdentifier());
    }

    public function testCanRetrieveItsSequenceNumber()
    {
        $this->assertSame(1, $this->event->sequenceNumber());
    }

    public function testCanRetrieveItsIdentifier()
    {
        $this->assertSame($this->identifier, $this->event->identifier());
    }

    public function testCanRetrieveItsPayload()
    {
        $this->assertSame($this->payload, $this->event->payload());
    }

    public function testCanRetrieveItsPayloadType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->event->payloadType());
    }

    public function testCanRetrieveItsMetadata()
    {
        $this->assertSame($this->metadata, $this->event->metadata());
    }

    public function testCanRetrieveItsMetadataType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->event->metadataType());
    }
}

class AggregateRootIdentifierStub implements AggregateRootIdentifier
{
    /**
     * @return string
     */
    public function toString()
    {
        return 'identifier';
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
