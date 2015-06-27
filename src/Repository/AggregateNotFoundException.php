<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;

class AggregateNotFoundException extends \RuntimeException
{
    public function __construct(AggregateRootIdentifier $identifier)
    {
        parent::__construct(sprintf('Aggregate with identifier [%s] could not be found.',
            $identifier->toString()));
    }
}
