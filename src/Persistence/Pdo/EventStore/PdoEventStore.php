<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\CompositeEventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\EventStore\EventStore;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\CreateQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\InsertQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\PdoEventStoreQuery;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\SelectQuery;
use RayRutjes\DomainFoundation\Repository\ConcurrencyException;
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
     * @param \PDO            $pdo
     * @param string          $tableName
     * @param EventSerializer $eventSerializer
     */
    public function __construct(\PDO $pdo, $tableName = 'events', EventSerializer $eventSerializer = null)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->eventSerializer = null === $eventSerializer ? new CompositeEventSerializer(new JsonSerializer()) : $eventSerializer;

        $this->insertQuery = new InsertQuery($pdo, $tableName);
        $this->selectQuery = new SelectQuery($pdo, $tableName);
        $this->createQuery = new CreateQuery($pdo, $tableName);
    }

    /**
     * @param Contract    $aggregateRootType
     * @param EventStream $eventStream
     *
     * @throws \Exception
     */
    public function append(Contract $aggregateRootType, EventStream $eventStream)
    {
        $statement = $this->insertQuery->prepare();

        try {
            while ($eventStream->hasNext()) {
                $event = $eventStream->next();

                $statement->bindValue(':aggregate_id', $event->aggregateRootIdentifier()->toString());
                $statement->bindValue(':aggregate_type', $aggregateRootType->toString());
                $statement->bindValue(':aggregate_version', $event->sequenceNumber());
                $statement->bindValue(':event_id', $event->identifier()->toString());
                $statement->bindValue(':event_payload', $this->eventSerializer->serializePayload($event));
                $statement->bindValue(':event_payload_type', $event->payloadType()->toString());
                $statement->bindValue(':event_metadata', $this->eventSerializer->serializeMetadata($event));
                $statement->bindValue(':event_metadata_type', $event->metadataType()->toString());

                $statement->execute();
                $statement->closeCursor();
            }
        } catch (\PDOException $exception) {
            // 23000 being the primary key duplicate entry error code.
            if ($exception->errorInfo[0] === '23000') {
                throw new ConcurrencyException('Concurrent modification detected.');
            }

            throw $exception;
        }
    }

    /**
     * @param Contract                $aggregateRootType
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     *
     * @return EventStream
     */
    public function read(Contract $aggregateRootType, AggregateRootIdentifier $aggregateRootIdentifier)
    {
        $statement = $this->selectQuery->prepare();

        $statement->bindValue(':aggregate_id', $aggregateRootIdentifier->toString());
        $statement->bindValue(':aggregate_type', $aggregateRootType->toString());

        $statement->execute();

        $records = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $statement->closeCursor();

        return new PdoReadRecordEventStream(
            $aggregateRootIdentifier,
            $records,
            $this->eventSerializer
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
