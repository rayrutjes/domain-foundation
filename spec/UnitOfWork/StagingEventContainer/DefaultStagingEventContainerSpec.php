<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;

class DefaultStagingEventContainerSpec extends ObjectBehavior
{
    public function let(EventBus $eventBus1, EventBus $eventBus2, Event $event1, Event $event2, Event $event3)
    {
        $this->add($event1, $eventBus1);
        $this->add($event2, $eventBus2);
        $this->add($event3, $eventBus2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\DefaultStagingEventContainer');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\StagingEventContainer');
    }

    public function it_can_return_all_staged_events($event1, $event2, $event3)
    {
        $this->all()->shouldEqual([$event1, $event2, $event3]);
    }

    public function it_can_publish_all_staged_events_to_the_event_buses_they_where_registered_with($eventBus1, $eventBus2, $event1, $event2, $event3)
    {
        $eventBus1->publish([$event1])->shouldBeCalledTimes(1);
        $eventBus2->publish([$event2, $event3])->shouldBeCalledTimes(1);
        $this->publishEvents();
    }

    public function it_should_clear_the_staged_events_once_published()
    {
        $this->publishEvents();
        $this->all()->shouldEqual([]);
    }

    public function it_can_clear_all_staged_events_without_publishing_them($eventBus1, $eventBus2)
    {
        $eventBus1->publish(Argument::any())->shouldNotBeCalled();
        $eventBus2->publish(Argument::any())->shouldNotBeCalled();
        $this->clear();
        $this->all()->shouldEqual([]);
    }
}
