<?php

namespace spec\RayRutjes\DomainFoundation\Persistence\Pdo\EventStore;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class PdoReadRecordEventStreamSpec extends ObjectBehavior
{
    public function let(
        AggregateRootIdentifier $aggregateRootIdentifier,
        EventSerializer $eventSerializer,
        ContractFactory $contractFactory,
        EventFactory $eventFactory,
        MessageIdentifierFactory $messageIdentifierFactory,
        Serializable $payload,
        Metadata $metadata,
        Event $event,
        Contract $contract,
        MessageIdentifier $messageIdentifier
    ) {
        $records = [
            [
                'event_id' => '',
                'event_payload' => '',
                'event_payload_type' => '',
                'event_metadata' => '',
                'event_metadata_type' => '',
                'aggregate_version' => '',
            ],
            [
                'event_id' => '',
                'event_payload' => '',
                'event_payload_type' => '',
                'event_metadata' => '',
                'event_metadata_type' => '',
                'aggregate_version' => '',
            ],
        ];

        $this->beConstructedWith($aggregateRootIdentifier, $records, $eventSerializer, $contractFactory, $eventFactory, $messageIdentifierFactory);
        $eventSerializer->deserializePayload(Argument::cetera())->willReturn($payload);
        $eventSerializer->deserializeMetadata(Argument::cetera())->willReturn($metadata);
        $eventFactory->create(Argument::cetera())->willReturn($event);
        $contractFactory->createFromClassName(Argument::any())->willReturn($contract);
        $messageIdentifierFactory->create(Argument::any())->willReturn($messageIdentifier);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoReadRecordEventStream');
    }

    public function it_tells_if_the_end_of_the_stream_has_been_reached()
    {
        $this->hasNext()->shouldReturn(true);
        $this->next();
        $this->hasNext()->shouldReturn(true);
        $this->next();
        $this->hasNext()->shouldReturn(false);
    }

    public function it_returns_the_current_event_and_advances_the_cursor($event)
    {
        $this->next()->shouldReturn($event);
        $this->next()->shouldReturn($event);
        $this->shouldThrow()->during('next');
    }

    public function it_can_return_the_current_event_of_the_stream($event)
    {
        $this->peek()->shouldReturn($event);
        $this->next();
        $this->peek()->shouldReturn($event);
    }

    public function it_tells_if_the_stream_is_not_empty()
    {
        $this->isEmpty()->shouldReturn(false);
    }

    public function it_tells_if_the_stream_is_empty(
        $aggregateRootIdentifier,
        $eventSerializer,
        $contractFactory,
        $eventFactory,
        $messageIdentifierFactory
    ) {
        $this->beConstructedWith(
            $aggregateRootIdentifier,
            [],
            $eventSerializer,
            $contractFactory,
            $eventFactory,
            $messageIdentifierFactory
        );
        $this->isEmpty()->shouldReturn(true);
    }
}
