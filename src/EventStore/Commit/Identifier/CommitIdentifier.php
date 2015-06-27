<?php

namespace RayRutjes\DomainFoundation\EventStore\Commit\Identifier;

interface CommitIdentifier
{
    /**
     * @return string
     */
    public function toString();
}
