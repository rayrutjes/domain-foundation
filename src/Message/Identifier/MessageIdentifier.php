<?php

namespace RayRutjes\DomainFoundation\Message\Identifier;

interface MessageIdentifier
{
    /**
     * @return string
     */
    public function toString();
}
