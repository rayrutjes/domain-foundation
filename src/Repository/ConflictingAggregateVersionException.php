<?php

namespace RayRutjes\DomainFoundation\Repository;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;

class ConflictingAggregateVersionException extends \RuntimeException
{
    /**
     * @var AggregateRootIdentifier
     */
    private $identifier;

    /**
     * @var int
     */
    private $expectedVersion;

    /**
     * @var int
     */
    private $actualVersion;

    /**
     * @param AggregateRootIdentifier $identifier
     * @param int                     $expectedVersion
     * @param int                     $actualVersion
     */
    public function __construct(AggregateRootIdentifier $identifier, $expectedVersion, $actualVersion)
    {
        $this->identifier = $identifier;
        $this->expectedVersion = (int) $expectedVersion;
        $this->actualVersion = (int) $actualVersion;

        parent::__construct(sprintf('Got an unexpected version [%d] instead of [%d] for aggregate [%s].',
            $this->actualVersion, $this->expectedVersion, $identifier->toString()));
    }

    /**
     * @return AggregateRootIdentifier
     */
    public function aggregateRootIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return int
     */
    public function expectedVersion()
    {
        return $this->expectedVersion;
    }

    /**
     * @return int
     */
    public function actualVersion()
    {
        return $this->actualVersion;
    }
}
