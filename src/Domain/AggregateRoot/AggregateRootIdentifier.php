<?php

namespace RayRutjes\DomainFoundation\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\ValueObject\ValueObject;

interface AggregateRootIdentifier extends ValueObject
{
    /**
     * @return string
     */
    public function toString();
}
