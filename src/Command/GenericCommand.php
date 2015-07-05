<?php

namespace RayRutjes\DomainFoundation\Command;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Message\GenericMessage;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

final class GenericCommand implements Command
{
    /**
     * @var GenericMessage
     */
    private $message;

    /**
     * @var string
     */
    private $commandName;

    /**
     * @param MessageIdentifier $identifier
     * @param Serializable      $payload
     * @param Metadata          $metadata
     */
    public function __construct(MessageIdentifier $identifier, Serializable $payload, Metadata $metadata = null)
    {
        $this->message = new GenericMessage($identifier, $payload, $metadata);
        $this->commandName = $this->payloadType()->className();
    }

    /**
     * Return the Command's name.
     *
     * @return string
     */
    public function commandName()
    {
        return $this->commandName;
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
