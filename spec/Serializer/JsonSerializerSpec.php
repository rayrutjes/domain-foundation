<?php

namespace spec\RayRutjes\DomainFoundation\Serializer;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Stub\Serializer\SerializableStub;

class JsonSerializerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Serializer\JsonSerializer');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Serializer\Serializer');
    }

    public function it_should_serialize_an_object()
    {
        $object = new SerializableStub();

        $this->serialize($object)->shouldReturn('{"name":"test","surname":"test"}');
    }

    public function it_should_deserialize_an_object(Contract $contract)
    {
        $contract->className()->shouldBeCalledTimes(1)->willReturn('RayRutjes\DomainFoundation\Stub\Serializer\SerializableStub');
        $object = $this->deserialize('{"name":"test","surname":"test"}', $contract);

        $object->shouldHaveType('RayRutjes\DomainFoundation\Stub\Serializer\SerializableStub');
        $object->name()->shouldReturn('test');
        $object->surname->shouldBe('test');
    }

    public function it_should_ignore_non_existent_parameter(Contract $contract)
    {
        $contract->className()->shouldBeCalledTimes(1)->willReturn('RayRutjes\DomainFoundation\Stub\Serializer\SerializableStub');
        $object = $this->deserialize('{"name":"test","surname":"test","firstname":"test"}', $contract);

        $this->serialize($object)->shouldEqual('{"name":"test","surname":"test"}');
    }
}
