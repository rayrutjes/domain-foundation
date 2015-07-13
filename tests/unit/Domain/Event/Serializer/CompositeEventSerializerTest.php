<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Domain\Event\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\CompositeEventSerializer;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class CompositeEventSerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeEventSerializer
     */
    private $eventSerializer;

    private $serializer;

    public function setUp()
    {
        $this->serializer = $this->getMockBuilder('RayRutjes\DomainFoundation\Serializer\Serializer')->getMock();
        $this->eventSerializer = new CompositeEventSerializer($this->serializer);
    }

    public function testSerializesPayload()
    {
        $event = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\Event')->getMock();

        $payload = new PayloadStub();
        $event->method('payload')
              ->willReturn($payload);

        $this->serializer->expects($this->once())
                         ->method('serialize')
                         ->with($this->identicalTo($payload))
                         ->willReturn('result');

        $this->assertEquals('result', $this->eventSerializer->serializePayload($event));
    }

    public function testDeserializesPayload()
    {
        $payload = new PayloadStub();
        $contract = Contract::createFromClassName('stdClass');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('{}', $contract)
            ->willReturn($payload);

        $this->assertSame($payload, $this->eventSerializer->deserializePayload('{}', $contract));
    }

    public function testSerializesMetadata()
    {
        $event = $this->getMockBuilder('RayRutjes\DomainFoundation\Domain\Event\Event')->getMock();

        $metadata = new Metadata();
        $event->method('metadata')
              ->willReturn($metadata);

        $this->serializer->expects($this->once())
             ->method('serialize')
             ->with($this->identicalTo($metadata))
             ->willReturn('result');

        $this->assertEquals('result', $this->eventSerializer->serializeMetadata($event));
    }

    public function testDeserializesMetadata()
    {
        $metadata = new Metadata();
        $contract = Contract::createFromClassName('stdClass');

        $this->serializer->expects($this->once())
            ->method('deserialize')
            ->with('{}', $contract)
            ->willReturn($metadata);

        $this->assertSame($metadata, $this->eventSerializer->deserializeMetadata('{}', $contract));
    }
}

class PayloadStub implements Serializable
{
}
