<?php

namespace RayRutjes\DomainFoundation\EventStore\Commit\Identifier\Factory;

use RayRutjes\DomainFoundation\EventStore\Commit\Identifier\CommitIdentifier;
use RayRutjes\DomainFoundation\EventStore\Commit\Identifier\UuidCommitIdentifier;

class UuidCommitIdentifierFactory implements CommitIdentifierFactory
{
    /**
     * @param string $identifier
     *
     * @return CommitIdentifier
     */
    public function create($identifier)
    {
        return new UuidCommitIdentifier($identifier);
    }

    /**
     * @return CommitIdentifier
     */
    public function generate()
    {
        return UuidCommitIdentifier::generate();
    }
}
