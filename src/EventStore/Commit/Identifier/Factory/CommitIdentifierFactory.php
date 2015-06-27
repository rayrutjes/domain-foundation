<?php

namespace RayRutjes\DomainFoundation\EventStore\Commit\Identifier\Factory;

use RayRutjes\DomainFoundation\EventStore\Commit\Identifier\CommitIdentifier;

interface CommitIdentifierFactory
{
    /**
     * @param string $identifier
     *
     * @return CommitIdentifier
     */
    public function create($identifier);

    /**
     * @return CommitIdentifier
     */
    public function generate();
}
