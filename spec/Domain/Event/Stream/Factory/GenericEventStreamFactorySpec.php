<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Stream\Factory;

use PhpSpec\ObjectBehavior;

class GenericEventStreamFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\GenericEventStreamFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\EventStreamFactory');
    }

    public function it_creates_a_generic_event_stream()
    {
        $this->create([])->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream');
    }
}
