<?php

namespace spec\RayRutjes\DomainFoundation\Domain\AggregateRoot;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Container\EventContainer;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Serializer\Serializable;
use RayRutjes\DomainFoundation\Stub\Domain\Event\Container\Factory\ProxyEventContainerFactoryStub;
use RayRutjes\DomainFoundation\Stub\Domain\Event\Stream\Factory\ProxyEventStreamFactoryStub;
use RayRutjes\DomainFoundation\ValueObject\ValueObject;
use Rhumsaa\Uuid\Uuid;

class EventSourcedAggregateRootSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('RayRutjes\DomainFoundation\Stub\Domain\AggregateRoot\EventSourcedAggregateRootStub');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Domain\AggregateRoot\EventSourcedAggregateRoot');
    }

    public function it_can_be_reconstituted_from_an_event_stream_history(EventStream $eventStream, Event $event)
    {
        $created = new Created(new UuidAggregateRootIdentifier());
        $deleted = new Deleted(new UuidAggregateRootIdentifier());

        $eventStream->hasNext()->willReturn(true, true, false);

        $eventStream->next()->willReturn($event);

        $event->sequenceNumber()->willReturn(1, 2);
        $event->payload()->willReturn($created, $deleted);

        $this->beConstructedThrough('loadFromHistory', [$eventStream]);
        $this->lastCommittedEventSequenceNumber()->shouldReturn(2);

        $this->isDeleted()->shouldReturn(true);
    }

    public function it_can_be_created(
        ProxyEventContainerFactoryStub $eventContainerFactory,
        EventContainer $eventContainer,
        ProxyEventStreamFactoryStub $eventStreamFactory,
        EventStream $eventStream
    ) {
        $this->beConstructedThrough('create', [new UuidAggregateRootIdentifier(), $eventContainerFactory, $eventStreamFactory]);

        $eventContainerFactory->proxy(Argument::any())->willReturn();
        $eventContainerFactory->create(Argument::any())->willReturn($eventContainer);

        $eventStreamFactory->proxy(Argument::any())->willReturn();
        $eventStreamFactory->create(Argument::any())->willReturn($eventStream);

        // The container should be initialized.
        $eventContainer->initializeSequenceNumber(0)->shouldBeCalledTimes(1);

        // The event added.
        $eventContainer->addEventFromPayload(Argument::any())->shouldBeCalledTimes(1);

        // The last committed should be 0 at tis stage, cause nothing has been committed yet.
        $eventContainer->lastCommittedSequenceNumber()->willReturn(0);
        $this->lastCommittedEventSequenceNumber()->shouldReturn(0);

        // We commit the changes.
        $eventContainer->lastSequenceNumber()->willReturn(1);

        // We ensure the container is cleared.
        $eventContainer->commit()->shouldBeCalled();

        $this->commitChanges();

        // Todo: test
    }

    public function it_can_attach_an_event_registration_callback_to_the_event_container(
        ProxyEventContainerFactoryStub $eventContainerFactory,
        EventContainer $eventContainer,
        ProxyEventStreamFactoryStub $eventStreamFactory,
        EventRegistrationCallback $callback
    ) {
        $this->beConstructedThrough('create', [new UuidAggregateRootIdentifier(), $eventContainerFactory, $eventStreamFactory]);

        $eventContainerFactory->proxy(Argument::any())->willReturn();
        $eventContainerFactory->create(Argument::any())->willReturn($eventContainer);

        $eventContainer->initializeSequenceNumber(Argument::any())->willReturn();
        $eventContainer->addEventFromPayload(Argument::any())->willReturn();

        $eventContainer->addRegistrationCallback($callback)->shouldBeCalledTimes(1);

        $this->addEventRegistrationCallback($callback);
    }

    public function it_can_compare_its_identity_with_another_aggregate_root(
        ProxyEventContainerFactoryStub $eventContainerFactory,
        EventContainer $eventContainer,
        ProxyEventStreamFactoryStub $eventStreamFactory,
        AggregateRoot $other
    ) {
        $this->beConstructedThrough('create', [new UuidAggregateRootIdentifier(), $eventContainerFactory, $eventStreamFactory]);

        $eventContainerFactory->proxy(Argument::any())->willReturn();
        $eventContainerFactory->create(Argument::any())->willReturn($eventContainer);

        $eventContainer->initializeSequenceNumber(Argument::any())->willReturn();
        $eventContainer->addEventFromPayload(Argument::any())->willReturn();

        $this->sameIdentityAs($other)->shouldReturn(false);
        $this->sameIdentityAs($this)->shouldReturn(true);
    }

    public function it_provides_the_uncommitted_changes(
        ProxyEventContainerFactoryStub $eventContainerFactory,
        EventContainer $eventContainer,
        ProxyEventStreamFactoryStub $eventStreamFactory,
        EventStream $eventStream
    ) {
        $this->beConstructedThrough('create', [new UuidAggregateRootIdentifier(), $eventContainerFactory, $eventStreamFactory]);

        $eventContainerFactory->proxy(Argument::any())->willReturn();
        $eventContainerFactory->create(Argument::any())->willReturn($eventContainer);

        $eventContainer->initializeSequenceNumber(Argument::any())->willReturn();
        $eventContainer->addEventFromPayload(Argument::any())->willReturn();
        $eventContainer->eventStream()->willReturn($eventStream);
        $eventContainer->lastSequenceNumber()->willReturn(1);
        $eventContainer->commit()->willReturn();

        $this->uncommittedChanges()->shouldReturn($eventStream);
        $this->commitChanges();
        $this->uncommittedChanges()->shouldReturn($eventStream);
    }

    public function it_should_provide_an_empty_stream_if_there_are_no_pending_changes(
        ProxyEventContainerFactoryStub $eventContainerFactory,
        EventContainer $eventContainer,
        ProxyEventStreamFactoryStub $eventStreamFactory,
        EventStream $eventStream)
    {
        $this->beConstructedThrough('wrongInitialization', []);
        $this->setEventStreamFactoryProxy($eventStreamFactory);
        $eventStreamFactory->create(Argument::any())->willReturn($eventStream);

        $this->uncommittedChanges()->shouldReturn($eventStream);
    }

    public function it_should_throw_an_exception_if_trying_to_access_a_non_initialized_identifier()
    {
        $this->beConstructedThrough('wrongInitialization', []);

        $this->shouldThrow()->during('identifier');
    }

    public function it_should_throw_an_exception_if_trying_to_override_an_identifier(AggregateRootIdentifier $identifier)
    {
        $this->beConstructedThrough('wrongInitialization', []);

        $this->overrideIdentifier($identifier);
        $this->shouldThrow()->during('overrideIdentifier', [$identifier]);
    }
}

class Created implements Serializable
{
    /**
     * @var AggregateRootIdentifier
     */
    private $identifier;

    /**
     * @param AggregateRootIdentifier $identifier
     */
    public function __construct(AggregateRootIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return AggregateRootIdentifier
     */
    public function identifier()
    {
        return $this->identifier;
    }
}

class Deleted implements Serializable
{
    /**
     * @var AggregateRootIdentifier
     */
    private $identifier;

    /**
     * @param AggregateRootIdentifier $identifier
     */
    public function __construct(AggregateRootIdentifier $identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return AggregateRootIdentifier
     */
    public function identifier()
    {
        return $this->identifier;
    }
}

class UuidAggregateRootIdentifier implements AggregateRootIdentifier
{
    /**
     * @return string
     */
    public function toString()
    {
        return Uuid::NIL;
    }

    /**
     * @param ValueObject $other
     *
     * @return bool
     */
    public function sameValueAs(ValueObject $other)
    {
        return $other->toString() === $this->toString();
    }
}
