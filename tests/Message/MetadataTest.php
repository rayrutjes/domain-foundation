<?php

namespace RayRutjes\DomainFoundation\Test\Message;

use RayRutjes\DomainFoundation\Message\Metadata;

class MetadataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Metadata
     */
    private $metadata;

    private $data;

    public function setUp()
    {
        $this->data = [
            'key1' => 'value1',
            'key2' => true,
        ];

        $this->metadata = new Metadata($this->data);
    }

    public function testImplementsSerializableInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Serializer\Serializable', $this->metadata);
    }

    public function testCanRetrieveAllTheKeyPairedValues()
    {
        $this->assertEquals($this->data, $this->metadata->all());
    }

    public function testCanRetrieveASpecificValueByItsKey()
    {
        $this->assertTrue($this->metadata->get('key2'));
    }

    public function testShouldReturnNullIfNoValueWasFoundForAGivenKey()
    {
        $this->assertNull($this->metadata->get('non_existant_key'));
    }

    public function testEnsuresThatTheImmutabilityIsPreservedWhenMergingData()
    {
        $metadata = $this->metadata->mergeWith([]);

        $this->assertNotSame($this->metadata, $metadata);
    }

    public function testCanBeMergedWithAdditionalData()
    {
        $additionalData = [
            'key2' => 'new_value',
            'key3' => 'value3',
        ];
        $metadata = $this->metadata->mergeWith($additionalData);

        $this->assertEquals([
            'key1' => 'value1',
            'key2' => 'new_value',
            'key3' => 'value3',
        ], $metadata->all());
    }
}
