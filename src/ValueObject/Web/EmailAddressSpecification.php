<?php

namespace RayRutjes\DomainFoundation\ValueObject\Web;

use RayRutjes\DomainFoundation\Specification\AbstractSpecification;

class EmailAddressSpecification extends AbstractSpecification
{
    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        if (!is_string($object)) {
            return false;
        }
        if (!filter_var($object, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }
}
