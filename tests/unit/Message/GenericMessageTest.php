<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Message;

use RayRutjes\DomainFoundation\Message\GenericMessage;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;

class GenericMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericMessage
     */
    private $message;

    private $identifier;

    private $payload;

    private $metadata;

    public function setUp()
    {
        $this->identifier = MessageIdentifier::generate();
        $this->payload = new PayloadStub();
        $this->metadata = new Metadata();

        $this->message = new GenericMessage($this->identifier, $this->payload, $this->metadata);
    }

    public function testCanRetrieveItsIdentifier()
    {
        $this->assertSame($this->identifier, $this->message->identifier());
    }

    public function testCanRetrieveItsPayload()
    {
        $this->assertSame($this->payload, $this->message->payload());
    }

    public function testCanRetrieveItsPayloadType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->message->payloadType());
    }

    public function testCanRetrieveItsMetadata()
    {
        $this->assertSame($this->metadata, $this->message->metadata());
    }

    public function testCanRetrieveItsMetadataType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->message->metadataType());
    }

    public function testCanReturnAMessageWithEnrichedMetadata()
    {
        $data = ['new_data' => 'new_data_value'];
        $enrichedMessage = $this->message->enrichMetadata($data);

        $this->assertNotSame($this->message, $enrichedMessage);
        $this->assertEquals($data, $enrichedMessage->metadata()->all());
        $this->assertSame($this->message->identifier(), $enrichedMessage->identifier());
        $this->assertSame($this->message->payload(), $enrichedMessage->payload());
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Message\GenericMessage', $enrichedMessage);
    }
}
