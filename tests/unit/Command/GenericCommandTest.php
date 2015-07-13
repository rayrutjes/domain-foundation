<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Command;

use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var GenericCommand
     */
    private $command;

    private $identifier;

    private $payload;

    private $metadata;

    public function setUp()
    {
        $this->identifier = MessageIdentifier::generate();
        $this->payload = new PayloadStub();
        $this->metadata = new Metadata();

        $this->command = new GenericCommand($this->identifier, $this->payload, $this->metadata);
    }

    public function testImplementsCommandInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\Command', $this->command);
    }

    public function testCanRetrieveItsName()
    {
        $this->assertEquals('RayRutjes\DomainFoundation\Test\Unit\Command\PayloadStub', $this->command->commandName());
    }

    public function testCanRetrieveItsIdentifier()
    {
        $this->assertSame($this->identifier, $this->command->identifier());
    }

    public function testCanRetrieveItsPayload()
    {
        $this->assertSame($this->payload, $this->command->payload());
    }

    public function testCanRetrieveItsPayloadType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->command->payloadType());
    }

    public function testCanRetrieveItsMetadata()
    {
        $this->assertSame($this->metadata, $this->command->metadata());
    }

    public function testCanRetrieveItsMetadataType()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $this->command->metadataType());
    }

    public function testCanReturnACommandWithEnrichedMetadata()
    {
        $data = ['new_data' => 'new_data_value'];
        $enrichedCommand = $this->command->enrichMetadata($data);

        $this->assertNotSame($this->command, $enrichedCommand);
        $this->assertEquals($data, $enrichedCommand->metadata()->all());
        $this->assertSame($this->command->identifier(), $enrichedCommand->identifier());
        $this->assertSame($this->command->payload(), $enrichedCommand->payload());
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\GenericCommand', $enrichedCommand);
    }
}

class PayloadStub implements Serializable
{
}
