<?php

namespace spec\RayRutjes\DomainFoundation\Message;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericMessageSpec extends ObjectBehavior
{
    public function let(MessageIdentifier $identifier, Serializable $payload, Metadata $metadata)
    {
        $this->beConstructedWith($identifier, $payload, $metadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Message\GenericMessage');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Message\Message');
    }

    public function it_can_return_its_identifier($identifier)
    {
        $this->identifier()->shouldReturn($identifier);
    }

    public function it_can_return_its_payload($payload)
    {
        $this->payload()->shouldReturn($payload);
    }

    public function it_can_return_its_payload_type()
    {
        $this->payloadType()->shouldHaveType('RayRutjes\DomainFoundation\Contract\Contract');
    }

    public function it_can_return_its_metadata($metadata)
    {
        $this->metadata()->shouldReturn($metadata);
    }

    public function it_should_return_an_empty_metadata_if_none_provided_at_initialization($identifier, $payload)
    {
        $this->beConstructedWith($identifier, $payload);

        $this->metadata()->shouldHaveType('RayRutjes\DomainFoundation\Message\Metadata');
    }

    public function it_can_return_its_metadata_type()
    {
        $this->metadataType()->shouldHaveType('RayRutjes\DomainFoundation\Contract\Contract');
    }
}
