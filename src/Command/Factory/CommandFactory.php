<?php

namespace RayRutjes\DomainFoundation\Command\Factory;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Contract\ContractFactory;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

interface CommandFactory
{
    /**
     * @param Serializable $payload
     *
     * @return Command
     */
    public function createFromPayload(Serializable $payload);

    /**
     * @param                   $commandName
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
    );
}
