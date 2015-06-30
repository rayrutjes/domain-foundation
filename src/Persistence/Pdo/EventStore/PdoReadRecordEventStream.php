<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Factory\EventFactory;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;

class PdoReadRecordEventStream implements EventStream
{
    /**
     * @var int
     */
    private $nextIndex = 0;

    /**
     * @var array
     */
    private $records;

    /**
     * @var EventSerializer
     */
    private $eventSerializer;

    /**
     * @var ContractFactory
     */
    private $contractFactory;

    /**
     * @var EventFactory
     */
    private $eventFactory;

    /**
     * @var AggregateRootIdentifier
     */
    private $aggregateRootIdentifier;

    /**
     * @var MessageIdentifierFactory
     */
    private $messageIdentifierFactory;

    /**
     * @param AggregateRootIdentifier  $aggregateRootIdentifier
     * @param array                    $records
     * @param EventSerializer          $eventSerializer
     * @param ContractFactory          $contractFactory
     * @param EventFactory             $eventFactory
     * @param MessageIdentifierFactory $messageIdentifierFactory
     */
    public function __construct(
        AggregateRootIdentifier $aggregateRootIdentifier,
        array $records,
        EventSerializer $eventSerializer,
        ContractFactory $contractFactory,
        EventFactory $eventFactory,
        MessageIdentifierFactory $messageIdentifierFactory
    ) {
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;
        $this->records = array_values($records); // Here we ensure keys are numeric and sequential.
        $this->eventSerializer = $eventSerializer;
        $this->contractFactory = $contractFactory;
        $this->eventFactory = $eventFactory;
        $this->messageIdentifierFactory = $messageIdentifierFactory;
    }

    /**
     * @return bool
     */
    public function hasNext()
    {
        return isset($this->records[$this->nextIndex]);
    }

    /**
     * @return Event
     */
    public function next()
    {
        $this->assertInBounds();
        $record = $this->records[$this->nextIndex++];

        return $this->translateRecordIntoEventMessage($record);
    }

    /**
     * @param array $record
     *
     * @return Event
     */
    private function translateRecordIntoEventMessage(array $record)
    {
        $identifier = $this->messageIdentifierFactory->create($record['event_id']);

        $payload = $this->eventSerializer->deserializePayload(
            $record['event_payload'],
            $this->contractFactory->createFromClassName($record['event_payload_type'])
        );

        $metadata = $this->eventSerializer->deserializeMetadata(
            $record['event_metadata'],
            $this->contractFactory->createFromClassName($record['event_metadata_type'])
        );

        $sequenceNumber = (int) $record['aggregate_version'];
//        $commitIdentifier = new CommitIdentifier($record['commit_id']);
//        $committedAt = new \DateTime($record['committed_at']);

        $eventMessage = $this->eventFactory->create(
            $this->aggregateRootIdentifier,
            $sequenceNumber,
            $identifier,
            $payload,
            $metadata,
            $this->contractFactory
        );

//        $eventMessage->setCommitIdentifier($commitIdentifier);
//        $eventMessage->setCommittedAt($committedAt);

        return $eventMessage;
    }

    /**
     * @return Event
     */
    public function peek()
    {
        $this->assertInBounds();
        $record = $this->records[$this->nextIndex];

        return $this->translateRecordIntoEventMessage($record);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->records);
    }

    private function assertInBounds()
    {
        if (!$this->hasNext()) {
            throw new \OutOfBoundsException('Event stream end reached.');
        }
    }
}
