<?php

namespace RayRutjes\DomainFoundation\Command;

use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Message\GenericMessage;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericCommand extends GenericMessage implements Command
{
    /**
     * @var string
     */
    private $commandName;

    /**
     * @param string            $commandName
     * @param MessageIdentifier $identifier
     * @param Serializable      $payload
     * @param Metadata          $metadata
     * @param ContractFactory   $contractFactory
     */
    public function __construct(
        $commandName,
        MessageIdentifier $identifier,
        Serializable $payload,
        Metadata $metadata = null,
        ContractFactory $contractFactory = null
    ) {
        parent::__construct($identifier, $payload, $metadata, $contractFactory);

        $this->commandName = $commandName;
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
}
