<?php

namespace RayRutjes\DomainFoundation\Message\Identifier\Factory;

use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Identifier\UuidMessageIdentifier;

class UuidMessageIdentifierFactory implements MessageIdentifierFactory
{
    /**
     * @param string $identifier
     *
     * @return MessageIdentifier
     */
    public function create($identifier)
    {
        return new UuidMessageIdentifier($identifier);
    }

    /**
     * @return MessageIdentifier
     */
    public function generate()
    {
        return UuidMessageIdentifier::generate();
    }
}
