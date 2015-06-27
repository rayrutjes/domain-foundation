<?php

namespace RayRutjes\DomainFoundation\Command\Factory;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class GenericCommandFactory implements CommandFactory
{
    /**
     * @var ContractFactory
     */
    private $contractFactory;

    /**
     * @var MessageIdentifierFactory
     */
    private $messageIdentifierFactory;

    /**
     * @param ContractFactory          $contractFactory
     * @param MessageIdentifierFactory $messageIdentifierFactory
     */
    public function __construct(ContractFactory $contractFactory, MessageIdentifierFactory $messageIdentifierFactory)
    {
        $this->contractFactory = $contractFactory;
        $this->messageIdentifierFactory = $messageIdentifierFactory;
    }

    /**
     * @param Serializable $payload
     *
     * @return Command
     */
    public function createFromPayload(Serializable $payload)
    {
        $contract = $this->contractFactory->createFromObject($payload);
        $commandName = $contract->className();
        $messageIdentifier = $this->messageIdentifierFactory->generate();

        return new GenericCommand($commandName, $messageIdentifier, $payload, new Metadata(), $this->contractFactory);
    }

    /**
     * @param string            $commandName
     * @param MessageIdentifier $messageIdentifier
     * @param Serializable      $payload
     * @param Metadata          $metadata
     * @param ContractFactory   $contractFactory
     *
     * @return Command
     */
    public function create(
        $commandName,
        MessageIdentifier $messageIdentifier,
        Serializable $payload,
        Metadata $metadata = null,
        ContractFactory $contractFactory = null
    ) {
        return new GenericCommand($commandName, $messageIdentifier, $payload, $metadata, $contractFactory);
    }
}
