<?php

namespace RayRutjes\DomainFoundation\Domain\Event;

use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Message\GenericMessage;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericEvent extends GenericMessage implements Event
{
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
     * @param ContractFactory         $contractFactory
     */
    public function __construct(
        AggregateRootIdentifier $aggregateRootIdentifier,
        $sequenceNumber,
        MessageIdentifier $identifier,
        Serializable $payload,
        Metadata $metadata = null,
        ContractFactory $contractFactory = null
    ) {
        parent::__construct($identifier, $payload, $metadata, $contractFactory);

        if (!is_int($sequenceNumber)) {
            throw new \InvalidArgumentException('Sequence number should be an integer.');
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
}
