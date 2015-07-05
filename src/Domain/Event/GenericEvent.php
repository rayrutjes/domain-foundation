<?php

namespace RayRutjes\DomainFoundation\Domain\Event;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Message\GenericMessage;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

final class GenericEvent implements Event
{
    /**
     * @var GenericMessage
     */
    private $message;

    /**
     * @var int
     */
    private $sequenceNumber;

    /**
     * @var AggregateRootIdentifier
     */
    private $aggregateRootIdentifier;

    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param int                     $sequenceNumber
     * @param MessageIdentifier       $identifier
     * @param Serializable            $payload
     * @param Metadata                $metadata
     */
    public function __construct(
        AggregateRootIdentifier $aggregateRootIdentifier,
        $sequenceNumber,
        MessageIdentifier $identifier,
        Serializable $payload,
        Metadata $metadata = null
    ) {
        $this->message = new GenericMessage($identifier, $payload, $metadata);

        if (!is_int($sequenceNumber) || $sequenceNumber < 1) {
            throw new \InvalidArgumentException('Sequence number should be an integer greater than zero.');
        }
        $this->sequenceNumber = $sequenceNumber;
        $this->aggregateRootIdentifier = $aggregateRootIdentifier;
    }

    /**
     * @return AggregateRootIdentifier
     */
    public function aggregateRootIdentifier()
    {
        return $this->aggregateRootIdentifier;
    }

    /**
     * @return int
     */
    public function sequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return MessageIdentifier
     */
    public function identifier()
    {
        return $this->message->identifier();
    }

    /**
     * @return Serializable
     */
    public function payload()
    {
        return $this->message->payload();
    }

    /**
     * @return Contract
     */
    public function payloadType()
    {
        return $this->message->payloadType();
    }

    /**
     * @return Metadata
     */
    public function metadata()
    {
        return $this->message->metadata();
    }

    /**
     * @return Contract
     */
    public function metadataType()
    {
        return $this->message->metadataType();
    }
}
