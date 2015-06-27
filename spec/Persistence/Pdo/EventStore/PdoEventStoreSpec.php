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
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\PdoEventStoreQuery;

class PdoEventStoreSpec extends ObjectBehavior
{
    private $tableName = 'events';

    public function let(
        \PDO $pdo,
        EventSerializer $eventSerializer,
        ContractFactory $contractFactory,
        PdoEventStoreQuery $query,
        EventFactory $eventFactory,
        MessageIdentifierFactory $messageIdentifierFactory
    ) {
        $this->beConstructedWith(
            $pdo,
            $this->tableName,
            $eventSerializer,
            $contractFactory,
            $query,
            $query,
            $query,
            $eventFactory,
            $messageIdentifierFactory
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoEventStore');
        $this->shouldHaveType('RayRutjes\DomainFoundation\EventStore\EventStore');
    }

    public function it_can_append_events_to_the_store(
        Contract $aggregateType,
        EventStream $eventStream,
        $query,
        \PDOStatement $statement,
        Event $event,
        AggregateRootIdentifier $aggregateRootIdentifier,
        MessageIdentifier $messageIdentifier
    ) {
        $query->prepare()->willReturn($statement);

        $eventStream->hasNext()->willReturn(true, false);
        $eventStream->next()->willReturn($event);

        $event->aggregateRootIdentifier()->willReturn($aggregateRootIdentifier);
        $event->sequenceNumber()->willReturn(Argument::any());
        $event->identifier()->willReturn($messageIdentifier);
        $event->payloadType()->willReturn($aggregateType);
        $event->metadataType()->willReturn($aggregateType);

        $statement->bindValue(Argument::any(), Argument::any())->shouldBeCalled();
        $statement->execute()->shouldBeCalled();
        $statement->closeCursor()->shouldBeCalled();

        $this->append($aggregateType, $eventStream);
    }

    public function it_can_return_an_event_stream_for_an_aggregate(Contract $aggregateType, AggregateRootIdentifier $aggregateRootIdentifier, $query, \PDOStatement $statement)
    {
        $query->prepare()->willReturn($statement);

        $statement->bindValue(Argument::any(), Argument::any())->shouldBeCalled();
        $statement->execute()->shouldBeCalled();
        $statement->fetchAll(Argument::any())->willReturn([]);
        $statement->closeCursor()->shouldBeCalled();

        $this->read($aggregateType, $aggregateRootIdentifier)->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoReadRecordEventStream');
    }

    public function it_can_create_underlying_event_store_table($query, \PDOStatement $statement)
    {
        $query->prepare()->shouldBeCalledTimes(1)->willReturn($statement);
        $statement->execute()->shouldBeCalledTimes(1);
        $statement->closeCursor()->shouldBeCalledTimes(1);
        $this->createTable();
    }
}
