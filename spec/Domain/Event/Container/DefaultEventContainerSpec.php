<?php

namespace spec\RayRutjes\DomainFoundation\Domain\Event\Container;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Domain\Event\Stream\Factory\EventStreamFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultEventContainerSpec extends ObjectBehavior
{
    public function let(
        AggregateRootIdentifier $aggregateRootIdentifier,
        EventFactory $eventFactory,
        MessageIdentifierFactory $messageIdentifierFactory,
        EventStreamFactory $eventStreamFactory,
        MessageIdentifier $messageIdentifier
    ) {
        $this->beConstructedWith(
            $aggregateRootIdentifier,
            $eventFactory,
            $messageIdentifierFactory,
            $eventStreamFactory
        );

        $messageIdentifierFactory->generate()->willReturn($messageIdentifier);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Container\DefaultEventContainer');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer');
    }

    public function it_can_return_an_event_stream_of_all_registered_events($eventStreamFactory, Event $event1, EventStream $eventStream)
    {
        $event1->sequenceNumber()->willReturn(1);
        $this->addEvent($event1);

        $eventStreamFactory->create([$event1])->willReturn($eventStream);
        $this->eventStream()->shouldReturn($eventStream);
    }

    public function it_can_add_an_event_by_providing_only_the_payload(Serializable $payload, $eventFactory, Event $event)
    {
        $event->sequenceNumber()->willReturn(1);
        $eventFactory->create(Argument::cetera())->willReturn($event);
        $this->addEventFromPayload($payload);
    }

    public function it_can_add_an_event(Event $event1, Event $event2, Event $event3)
    {
        $event1->sequenceNumber()->willReturn(1);
        $event2->sequenceNumber()->willReturn(2);
        $event3->sequenceNumber()->willReturn(99);

        $this->addEvent($event1);
        $this->lastSequenceNumber()->shouldReturn(1);
        $this->addEvent($event2);
        $this->lastSequenceNumber()->shouldReturn(2);

        $this->shouldThrow(new \Exception('Corrupted sequence number.'))->during('addEvent', [$event3]);
    }

    public function it_should_be_cleared_when_committed_and_last_committed_sequence_number_should_be_preserved(Event $event1, Event $event2)
    {
        $event1->sequenceNumber()->willReturn(1);
        $event2->sequenceNumber()->willReturn(2);
        $this->addEvent($event1);
        $this->addEvent($event2);
        $this->commit();
        $this->lastCommittedSequenceNumber()->shouldReturn(2);
        $this->lastSequenceNumber()->shouldReturn(2);
        $this->count()->shouldReturn(0);
    }

    public function it_can_return_the_total_amount_of_events_in_the_container(Event $event1, Event $event2)
    {
        $event1->sequenceNumber()->willReturn(1);
        $event2->sequenceNumber()->willReturn(2);
        $this->addEvent($event1);
        $this->addEvent($event2);
        $this->count()->shouldReturn(2);
    }

    public function it_can_initialize_its_last_committed_sequence_number(Event $event1)
    {
        $this->initializeSequenceNumber(10);
        $this->lastSequenceNumber()->shouldReturn(10);
        $this->lastCommittedSequenceNumber()->shouldReturn(10);

        $this->shouldThrow(new \InvalidArgumentException('Sequence number should be a positive integer.'))->during('initializeSequenceNumber', ['10']);
        $this->shouldThrow(new \InvalidArgumentException('Sequence number should be a positive integer.'))->during('initializeSequenceNumber', [0]);
        $this->shouldThrow(new \InvalidArgumentException('Sequence number should be a positive integer.'))->during('initializeSequenceNumber', [-9]);
        $this->shouldThrow(new \InvalidArgumentException('Sequence number should be a positive integer.'))->during('initializeSequenceNumber', [null]);

        $event1->sequenceNumber()->willReturn(11);
        $this->addEvent($event1);

        $this->shouldThrow(new \LogicException('Can not initialize sequence number if events have already been added.'))->during('initializeSequenceNumber', [30]);
    }

    public function it_should_apply_registration_callbacks_on_registered_events(Event $event1, Event $event2, Event $event3, Event $event4, Event $event5, EventRegistrationCallback $callback, EventRegistrationCallback $callback2)
    {
        $event1->sequenceNumber()->willReturn(1);
        $event2->sequenceNumber()->willReturn(2);
        $event3->sequenceNumber()->willReturn(1);
        $event4->sequenceNumber()->willReturn(2);

        $this->addEvent($event1);
        $this->addEvent($event2);

        $callback->onEventRegistration($event1)->shouldBeCalledTimes(1)->willReturn($event3);
        $callback->onEventRegistration($event2)->shouldBeCalledTimes(1)->willReturn($event4);
        $this->addRegistrationCallback($callback);

        $callback2->onEventRegistration($event3)->shouldBeCalledTimes(1)->willReturn($event3);
        $callback2->onEventRegistration($event4)->shouldBeCalledTimes(1)->willReturn($event4);
        $this->addRegistrationCallback($callback2);

        $event5->sequenceNumber()->willReturn(3);
        $callback->onEventRegistration($event5)->shouldBeCalledTimes(1)->willReturn($event5);
        $callback2->onEventRegistration($event5)->shouldBeCalledTimes(1)->willReturn($event5);
        $this->addEvent($event5);
    }
}
