<?php

namespace spec\RayRutjes\DomainFoundation\Repository;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;

class ConflictingAggregateVersionExceptionSpec extends ObjectBehavior
{
    public function let(AggregateRootIdentifier $identifier)
    {
        $this->beConstructedWith($identifier, 1, 2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Repository\ConflictingAggregateVersionException');
    }

    public function it_provides_the_aggregate_root_identifier($identifier)
    {
        $this->aggregateRootIdentifier()->shouldReturn($identifier);
    }

    public function it_provides_the_expected_version_sequence_number()
    {
        $this->expectedVersion()->shouldReturn(1);
    }

    public function it_provides_the_actual_version_sequence_number()
    {
        $this->actualVersion()->shouldReturn(2);
    }
}
