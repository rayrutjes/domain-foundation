<?php

namespace RayRutjes\DomainFoundation\Domain\Event;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Message\Message;

interface Event extends Message
{
    /**
     * @return AggregateRootIdentifier
     */
    public function aggregateRootIdentifier();

    /**
     * @return int
     */
    public function sequenceNumber();
}
