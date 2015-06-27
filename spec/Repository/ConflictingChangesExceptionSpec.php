<?php

namespace spec\RayRutjes\DomainFoundation\Repository;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;

class ConflictingChangesExceptionSpec extends ObjectBehavior
{
    public function let(EventStream $conflicting, EventStream $committed)
    {
        $this->beConstructedWith($conflicting, $committed);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Repository\ConflictingChangesException');
    }

    public function it_provides_the_conflicting_event_stream($conflicting)
    {
        $this->conflictingEventStream()->shouldReturn($conflicting);
    }

    public function it_provides_the_committed_event_stream($committed)
    {
        $this->committedEventStream()->shouldReturn($committed);
    }
}
