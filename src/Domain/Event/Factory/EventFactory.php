<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Factory;

use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

interface EventFactory
{
    /**
     * @param AggregateRootIdentifier $aggregateRootIdentifier
     * @param int                     $sequenceNumber
     * @param MessageIdentifier       $identifier
     * @param Serializable            $payload
     * @param Metadata                $metadata
     * @param ContractFactory         $contractFactory
     *
     * @return Event
     */
    public function create(
        AggregateRootIdentifier $aggregateRootIdentifier,
        $sequenceNumber,
        MessageIdentifier $identifier,
        Serializable $payload,
        Metadata $metadata = null,
        ContractFactory $contractFactory = null
    );
}
