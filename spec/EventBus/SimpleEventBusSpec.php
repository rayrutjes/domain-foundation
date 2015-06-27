<?php

namespace spec\RayRutjes\DomainFoundation\EventBus;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\Listener\EventListener;

class SimpleEventBusSpec extends ObjectBehavior
{
    public function let(
        EventListener $listener1,
        EventListener $listener2,
        Event $event1,
        Event $event2
    ) {
        $this->subscribe($listener1);
        $this->subscribe($listener2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\EventBus\SimpleEventBus');
        $this->shouldHaveType('RayRutjes\DomainFoundation\EventBus\EventBus');
    }

    public function it_should_publish_events_to_the_subscribed_listeners(
        $listener1,
        $listener2,
        $event1,
        $event2
    ) {
        $listener1->handle($event1)->shouldBeCalledTimes(1);
        $listener2->handle($event1)->shouldBeCalledTimes(1);

        $listener1->handle($event2)->shouldBeCalledTimes(1);
        $listener2->handle($event2)->shouldBeCalledTimes(1);

        $this->publish([$event1, $event2]);

        $this->shouldThrow(new \InvalidArgumentException('Only [RayRutjes\DomainFoundation\Domain\Event\Event] can be published.'))->during('publish', [[new \stdClass()]]);
    }

    public function it_should_not_publish_events_to_unsubscribed_listeners(
        $listener1,
        $listener2,
        $event1,
        $event2
    ) {
        $listener1->handle(Argument::any())->shouldNotBeCalled();

        $listener2->handle($event1)->shouldBeCalledTimes(1);
        $listener2->handle($event2)->shouldBeCalledTimes(1);

        $this->unsubscribe($listener1);

        $this->publish([$event1, $event2]);
    }

    public function it_should_not_subscribe_already_subscribed_listeners($listener1)
    {
        $this->shouldThrow(new \Exception('This listener instance is already subscribed.'))->during('subscribe', [$listener1]);
    }

    public function it_should_not_unsubscribe_already_unsubscribed_listeners($listener1)
    {
        $this->unsubscribe($listener1);

        $this->shouldThrow(new \Exception('This listener instance is not subscribed.'))->during('unsubscribe', [$listener1]);
    }
}
