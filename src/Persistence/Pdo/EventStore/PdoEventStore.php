<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Contract\ConventionalContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Factory\GenericEventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\CompositeEventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\EventStore\CommitIdentifier;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\UuidMessageIdentifierFactory;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\CreateQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\InsertQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\PdoEventStoreQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\SelectQuery;
use RayRutjes\DomainFoundation\Serializer\JsonSerializer;

final class PdoEventStore implements EventStore
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var EventSerializer
     */
    private $eventSerializer;

    /**
     * @var ContractFactory
     */
    private $contractFactory;

    /**
     * @var PdoEventStoreQuery
     */
    private $insertQuery;

    /**
     * @var PdoEventStoreQuery
     */
    private $selectQuery;

    /**
     * @var PdoEventStoreQuery
     */
    private $createQuery;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var MessageIdentifierFactory
     */
    private $messageIdentifierFactory;

    /**
     * @param \PDO                     $pdo
     * @param string                   $tableName
     * @param EventSerializer          $eventSerializer
     * @param ContractFactory          $contractFactory
     * @param PdoEventStoreQuery       $insertQuery
     * @param PdoEventStoreQuery       $selectQuery
     * @param PdoEventStoreQuery       $createQuery
     * @param EventFactory             $eventFactory
     * @param MessageIdentifierFactory $messageIdentifierFactory
     */
    public function __construct(
        \PDO $pdo,
        $tableName = 'events',
        EventSerializer $eventSerializer = null,
        ContractFactory $contractFactory = null,
        PdoEventStoreQuery $insertQuery = null,
        PdoEventStoreQuery $selectQuery = null,
        PdoEventStoreQuery $createQuery = null,
        EventFactory $eventFactory = null,
        MessageIdentifierFactory $messageIdentifierFactory = null
    ) {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->eventSerializer = null === $eventSerializer ? new CompositeEventSerializer(new JsonSerializer()) : $eventSerializer;
        $this->contractFactory = null === $contractFactory ? new ConventionalContractFactory() : $contractFactory;
        $this->insertQuery = null === $insertQuery ? new InsertQuery($pdo, $tableName) : $insertQuery;
        $this->selectQuery = null === $selectQuery ? new SelectQuery($pdo, $tableName) : $selectQuery;
        $this->createQuery = null === $createQuery ? new CreateQuery($pdo, $tableName) : $createQuery;
        $this->eventFactory = null === $eventFactory ? new GenericEventFactory() : $eventFactory;
        $this->messageIdentifierFactory = null === $messageIdentifierFactory ? new UuidMessageIdentifierFactory() : $messageIdentifierFactory;
    }

    /**
     * @param Contract    $aggregateType
     * @param EventStream $eventStream
     *
     * @throws \Exception
     */
    public function append(Contract $aggregateType, EventStream $eventStream)
    {
        $statement = $this->insertQuery->prepare();

        $commitIdentifier = CommitIdentifier::generate()->toString();
        $committedAt = new \DateTime();

        while ($eventStream->hasNext()) {
            $event = $eventStream->next();

            $statement->bindValue(':aggregate_id', $event->aggregateRootIdentifier()->toString());
            $statement->bindValue(':aggregate_type', $aggregateType->toString());
            $statement->bindValue(':aggregate_version', $event->sequenceNumber());
            $statement->bindValue(':event_id', $event->identifier()->toString());
            $statement->bindValue(':event_payload', $this->eventSerializer->serializePayload($event));
            $statement->bindValue(':event_payload_type', $event->payloadType()->toString());
            $statement->bindValue(':event_metadata', $this->eventSerializer->serializeMetadata($event));
            $statement->bindValue(':event_metadata_type', $event->metadataType()->toString());
            $statement->bindValue(':commit_id', $commitIdentifier);
            $statement->bindValue(':committed_at', $committedAt->format('UTC'));

            $statement->execute();
            $statement->closeCursor();
        }
    }

    /**
     * @param Contract                $aggregateType
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventStream
     */
    public function read(Contract $aggregateType, AggregateRootIdentifier $aggregateRootIdentifier)
    {
        $statement = $this->selectQuery->prepare();

        $statement->bindValue(':aggregate_id', $aggregateRootIdentifier->toString());
        $statement->bindValue(':aggregate_type', $aggregateType->toString());

        $statement->execute();

        $records = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return new PdoReadRecordEventStream(
            $aggregateRootIdentifier,
            $records,
            $this->eventSerializer,
            $this->contractFactory,
            $this->eventFactory,
            $this->messageIdentifierFactory
        );
    }

    /**
     * Create the database table.
     */
    public function createTable()
    {
        $statement = $this->createQuery->prepare();
        $statement->execute();
        $statement->closeCursor();
    }
}
