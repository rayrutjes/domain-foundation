<?php

namespace RayRutjes\DomainFoundation\Message;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Serializer\Serializable;

final class GenericMessage implements Message
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
     * @var Contract
     */
    private $payloadType;

    /**
     * @var Metadata
     */
    private $metadata;

    /**
     * @var Contract
     */
    private $metadataType;

    /**
     * @param MessageIdentifier $identifier
     * @param Serializable      $payload
     * @param Metadata          $metadata
     */
    public function __construct(MessageIdentifier $identifier, Serializable $payload, Metadata $metadata = null)
    {
        $this->identifier = $identifier;

        $this->payload = $payload;
        $this->payloadType = Contract::createFromObject($this->payload);

        $this->metadata = null === $metadata ? new Metadata() : $metadata;
        $this->metadataType = Contract::createFromObject($this->metadata);
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
        return $this->payloadType;
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
        return $this->metadataType;
    }
}
