<?php

namespace RayRutjes\DomainFoundation\Test\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Serializer\JsonSerializer;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testCanSerializeAsJson()
    {
        $serializer = new JsonSerializer();
        $this->assertEquals(
            '{"publicValue":"public","privateValue":3}',
            $serializer->serialize(new JsonSerializableStub())
        );
    }

    public function testCanDeserializeAJsonSerializedString()
    {
        $serializer = new JsonSerializer();
        $stub = new JsonSerializableStub();

        $contract = Contract::createFromObject($stub);

        $this->assertEquals(
            $stub,
            $serializer->deserialize('{"publicValue":"public","privateValue":3}', $contract)
        );
    }

    public function testSkipsUnknownPropertiesWhenDeserializing()
    {
        $serializer = new JsonSerializer();
        $stub = new JsonSerializableStub();

        $contract = Contract::createFromObject($stub);

        $this->assertEquals(
            $stub,
            $serializer->deserialize('{"publicValue":"public","privateValue":3, "unknown":99}', $contract)
        );
    }
}

class JsonSerializableStub implements Serializable
{
    public $publicValue = 'public';
    private $privateValue = 3;
}
