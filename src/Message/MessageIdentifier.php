<?php

namespace RayRutjes\DomainFoundation\Message;

use RayRutjes\DomainFoundation\ValueObject\Identity\Uuid;

final class MessageIdentifier
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
     * @return MessageIdentifier
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
     * @param MessageIdentifier $identifier
     *
     * @return bool
     */
    public function equals(MessageIdentifier $identifier)
    {
        return $this->uuid->toString() === $identifier->toString();
    }
}
