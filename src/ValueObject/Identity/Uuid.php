<?php

namespace RayRutjes\DomainFoundation\ValueObject\Identity;

use InvalidArgumentException;
use RayRutjes\DomainFoundation\ValueObject\ValueObject;

class Uuid implements ValueObject
{
    /**
     * @var \Rhumsaa\Uuid\Uuid
     */
    private $uuid;

    /**
     * @return Uuid
     */
    final public static function generate()
    {
        return new static(\Rhumsaa\Uuid\Uuid::uuid4()->toString());
    }

    /**
     * @param string
     */
    final public function __construct($uuid)
    {
        if (!is_string($uuid)) {
            throw new InvalidArgumentException('Uuid expected a string.');
        }
        if (!\Rhumsaa\Uuid\Uuid::isValid($uuid)) {
            throw new InvalidArgumentException('Invalid Uuid format.');
        }

        $this->uuid = \Rhumsaa\Uuid\Uuid::fromString($uuid);
    }

    /**
     * @param ValueObject $other
     *
     * @return bool
     */
    final public function sameValueAs(ValueObject $other)
    {
        if (!$other instanceof self) {
            return false;
        }

        return $this->toString() === $other->toString();
    }

    /**
     * @return string
     */
    final public function toString()
    {
        return $this->uuid->toString();
    }

    /**
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }
}
