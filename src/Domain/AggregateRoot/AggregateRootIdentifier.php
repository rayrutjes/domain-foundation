<?php

namespace RayRutjes\DomainFoundation\Domain\AggregateRoot;

interface AggregateRootIdentifier
{
    /**
     * @return string
     */
    public function toString();

    /**
     * @param AggregateRootIdentifier $identifier
     *
     * @return mixed
     */
    public function equals(AggregateRootIdentifier $identifier);
}
