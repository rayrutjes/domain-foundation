<?php

namespace RayRutjes\DomainFoundation\ValueObject\Identity;

final class Uuid
{
    /**
     * @var \Rhumsaa\Uuid\Uuid
     */
    private $uuid;

    /**
     * @param string
     *
     * @throws InvalidArgumentException
     */
    public function __construct($uuid)
    {
        $this->assertIsString($uuid);
        $this->assertIsValidUuid($uuid);

        $this->uuid = \Rhumsaa\Uuid\Uuid::fromString($uuid);
    }

    /**
     * @param string $uuid
     */
    private function assertIsString($uuid)
    {
        if (!is_string($uuid)) {
            throw new \InvalidArgumentException('Uuid expected a string.');
        }
    }

    /**
     * @param string $uuid
     */
    private function assertIsValidUuid($uuid)
    {
        if (!\Rhumsaa\Uuid\Uuid::isValid($uuid)) {
            throw new \InvalidArgumentException('Invalid Uuid format.');
        }
    }

    /**
     * @return Uuid
     */
    public static function generate()
    {
        return new self(\Rhumsaa\Uuid\Uuid::uuid4()->toString());
    }

    /**
     * @param Uuid $other
     *
     * @return bool
     */
    public function equals(Uuid $other)
    {
        return $this->toString() === $other->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->uuid->toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}
