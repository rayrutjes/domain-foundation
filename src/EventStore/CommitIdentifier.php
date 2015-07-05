<?php

namespace RayRutjes\DomainFoundation\EventStore;

use RayRutjes\DomainFoundation\ValueObject\Identity\Uuid;

final class CommitIdentifier
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @param string|Uuid $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid instanceof Uuid ? $uuid : new Uuid($uuid);
    }

    /**
     * @return CommitIdentifier
     */
    public static function generate()
    {
        return new self(Uuid::generate());
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->uuid->toString();
    }

    /**
     * @param CommitIdentifier $identifier
     *
     * @return bool
     */
    public function equals(CommitIdentifier $identifier)
    {
        return $this->uuid->toString() === $identifier->toString();
    }
}
