<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer;
use RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;

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
     * @var AggregateRootIdentifier
     */
    private $aggregateRootIdentifier;

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param array                   $records
     * @param EventSerializer         $eventSerializer
     */
    public function __construct(
        AggregateRootIdentifier $aggregateRootIdentifier,
        array $records,
        EventSerializer $eventSerializer
    ) {
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;
        $this->records = array_values($records); // Here we ensure keys are numeric and sequential.
        $this->eventSerializer = $eventSerializer;
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
        $identifier = new MessageIdentifier($record['event_id']);

        $payload = $this->eventSerializer->deserializePayload(
            $record['event_payload'],
            Contract::createFromClassName($record['event_payload_type'])
        );

        $metadata = $this->eventSerializer->deserializeMetadata(
            $record['event_metadata'],
            Contract::createFromClassName($record['event_metadata_type'])
        );

        $sequenceNumber = (int) $record['aggregate_version'];
//        $commitIdentifier = new CommitIdentifier($record['commit_id']);
//        $committedAt = new \DateTime($record['committed_at']);

        $eventMessage = new GenericEvent($this->aggregateRootIdentifier, $sequenceNumber, $identifier, $payload, $metadata);

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
