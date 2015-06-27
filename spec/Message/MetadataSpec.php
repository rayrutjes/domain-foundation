<?php

namespace spec\RayRutjes\DomainFoundation\Message;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Message\Metadata;

class MetadataSpec extends ObjectBehavior
{
    private $metadata;

    public function let()
    {
        $this->metadata = ['key' => 'value'];
        $this->beConstructedWith($this->metadata);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Message\Metadata');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Serializer\Serializable');
    }

    public function it_can_return_the_key_paired_data()
    {
        $this->all()->shouldReturn($this->metadata);
    }

    public function it_can_return_a_value_by_its_key_and_return_null_if_it_does_not_exist()
    {
        $this->get('key')->shouldReturn('value');
        $this->get('nothing')->shouldReturn(null);
    }

    public function it_can_be_merged_with_some_data_by_preserving_its_invariants(Metadata $metadata)
    {
        $this->mergeWith(['other_key' => 'other_value'])->shouldHaveType('RayRutjes\DomainFoundation\Message\Metadata');
        $this->all()->shouldReturn($this->metadata);
    }
}
