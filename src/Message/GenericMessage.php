<?php

namespace RayRutjes\DomainFoundation\Message;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Contract\ConventionalContractFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericMessage implements Message
{
    /**
     * @var MessageIdentifier
     */
    private $identifier;

    /**
     * @var Serializable
     */
    private $payload;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var ContractFactory
     */
    private $contractFactory;

    /**
     * @param MessageIdentifier $identifier
     * @param Serializable      $payload
     * @param Metadata          $metadata
     * @param ContractFactory   $contractFactory
     */
    public function __construct(MessageIdentifier $identifier, Serializable $payload, Metadata $metadata = null, ContractFactory $contractFactory = null)
    {
        $this->identifier = $identifier;
        $this->payload = $payload;
        $this->metadata = null === $metadata ? new Metadata() : $metadata;
        $this->contractFactory = null === $contractFactory ? new ConventionalContractFactory() : $contractFactory;
    }

    /**
     * @return Serializable
     */
    public function payload()
    {
        return $this->payload;
    }

    /**
     * @return MessageIdentifier
     */
    public function identifier()
    {
        return $this->identifier;
    }

    /**
     * @return Contract
     */
    public function payloadType()
    {
        return $this->contractFactory->createFromObject($this->payload);
    }

    /**
     * @return Metadata
     */
    public function metadata()
    {
        return $this->metadata;
    }

    /**
     * @return Contract
     */
    public function metadataType()
    {
        return $this->contractFactory->createFromObject($this->metadata);
    }
}
