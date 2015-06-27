<?php

namespace RayRutjes\DomainFoundation\Message\Identifier\Factory;

use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;

interface MessageIdentifierFactory
{
    /**
     * @param string $identifier
     *
     * @return MessageIdentifier
     */
    public function create($identifier);

    /**
     * @return MessageIdentifier
     */
    public function generate();
}
