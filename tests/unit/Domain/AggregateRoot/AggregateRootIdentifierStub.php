<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use Rhumsaa\Uuid\Uuid;

class AggregateRootIdentifierStub implements AggregateRootIdentifier
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param string $uid
     */
    public function __construct($uid = null)
    {
        if(null === $uid) {
            $uid = Uuid::NIL;
        }
        $this->identifier = (string) $uid;
    }
    /**
     * @return string
     */
    public function toString()
    {
        return $this->identifier;
    }

    /**
     * @param AggregateRootIdentifier $identifier
     *
     * @return mixed
     */
    public function equals(AggregateRootIdentifier $identifier)
    {
        if (!$identifier instanceof self) {
            return false;
        }

        return $this->identifier === $identifier->toString();
    }
}
