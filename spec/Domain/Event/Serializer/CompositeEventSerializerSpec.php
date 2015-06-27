<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Serializer;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;
use RayRutjes\DomainFoundation\Serializer\Serializer;

class CompositeEventSerializerSpec extends ObjectBehavior
{
    public function let(Serializer $serializer)
    {
        $this->beConstructedWith($serializer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Serializer\CompositeEventSerializer');
    }

    public function it_can_serialize_payload($serializer, Event $event, Serializable $payload)
    {
        $event->payload()->shouldBeCalledTimes(1)->willReturn($payload);

        $serializer->serialize($payload)->shouldBeCalledTimes(1)->willReturn('{}');

        $this->serializePayload($event)->shouldReturn('{}');
    }

    public function it_can_deserialize_payload($serializer, Contract $contract, Serializable $serializable)
    {
        $serializer->deserialize('{}', $contract)->shouldBeCalledTimes(1)->willReturn($serializable);

        $this->deserializePayload('{}', $contract)->shouldReturn($serializable);
    }

    public function it_can_serialize_metadata($serializer, Event $event, Metadata $metadata)
    {
        $event->metadata()->shouldBeCalledTimes(1)->willReturn($metadata);

        $serializer->serialize($metadata)->shouldBeCalledTimes(1)->willReturn('{}');

        $this->serializeMetadata($event)->shouldReturn('{}');
    }

    public function it_can_deserialize_metadata($serializer, Contract $contract, Metadata $metadata)
    {
        $serializer->deserialize('{}', $contract)->shouldBeCalledTimes(1)->willReturn($metadata);

        $this->deserializeMetadata('{}', $contract)->shouldReturn($metadata);
    }
}
