<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Stream;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\Event\Event;

class GenericEventStreamSpec extends ObjectBehavior
{
    public function let(Event $event1, Event $event2)
    {
        $this->beConstructedWith([$event1, $event2]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream');
    }

    public function it_can_only_contain_events()
    {
        $this->shouldThrow(new \InvalidArgumentException('Stream should only contain Event objects.'))->during('__construct', [[new \stdClass()]]);
    }

    public function it_can_peek_the_current_event($event1, $event2)
    {
        $this->peek()->shouldReturn($event1);
        $this->next();
        $this->peek()->shouldReturn($event2);
    }

    public function it_can_peek_an_event_and_increment_the_cursor($event1, $event2)
    {
        $this->next()->shouldReturn($event1);
        $this->next()->shouldReturn($event2);

        $this->shouldThrow(new \OutOfBoundsException('You reached the end of the stream.'))->during('next');
    }

    public function it_can_tell_if_the_stream_has_reached_the_end()
    {
        $this->hasNext()->shouldReturn(true);
        $this->next();
        $this->hasNext()->shouldReturn(true);
        $this->next();
        $this->hasNext()->shouldReturn(false);
    }

    public function it_can_tell_if_the_stream_is_not_empty()
    {
        $this->isEmpty()->shouldReturn(false);
    }

    public function it_can_tell_if_the_stream_is_empty()
    {
        $this->beConstructedWith([]);
        $this->isEmpty()->shouldReturn(true);

        $this->shouldThrow(new \OutOfBoundsException('You reached the end of the stream.'))->during('peek');
    }
}
