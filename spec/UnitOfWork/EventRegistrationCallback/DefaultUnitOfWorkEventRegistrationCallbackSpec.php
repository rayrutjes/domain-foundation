<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultUnitOfWorkEventRegistrationCallbackSpec extends ObjectBehavior
{
    public function let(UnitOfWork $unitOfWork, EventBus $eventBus)
    {
        $this->beConstructedWith($unitOfWork, $eventBus);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\DefaultUnitOfWorkEventRegistrationCallback');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback');
    }

    public function it_should_publish_a_newly_registered_event_and_return_it_afterwards($unitOfWork, $eventBus, Event $event)
    {
        $unitOfWork->publishEvent($event, $eventBus)->shouldBeCalledTimes(1);
        $unitOfWork->invokeEventRegistrationListeners($event)->shouldBeCalledTimes(1)->willReturn($event);
        $this->onEventRegistration($event)->shouldReturn($event);
    }
}
