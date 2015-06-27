<?php

namespace RayRutjes\DomainFoundation\ValueObject\Web;

use InvalidArgumentException;
use RayRutjes\DomainFoundation\ValueObject\ValueObject;

class EmailAddress implements ValueObject
{
    /**
     * @var string
     */
    private $address;

    /**
     * @param string $address
     */
    final public function __construct($address)
    {
        $specification = new EmailAddressSpecification();
        if (!$specification->isSatisfiedBy($address)) {
            throw new InvalidArgumentException('EmailAddress expects a well formatted email address.');
        }

        $this->address = $address;
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
        return $this->address;
    }

    /**
     * @return string
     */
    final public function __toString()
    {
        return $this->toString();
    }
}
