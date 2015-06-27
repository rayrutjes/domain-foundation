<?php

namespace RayRutjes\DomainFoundation\Stub\Specification;

use RayRutjes\DomainFoundation\Specification\AbstractSpecification;

class SpecificationStub extends AbstractSpecification
{
    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return true;
    }
}
