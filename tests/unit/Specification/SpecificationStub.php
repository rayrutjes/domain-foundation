<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Specification;

use RayRutjes\DomainFoundation\Specification\Specification;

class SpecificationStub implements Specification
{
    /**
     * @var bool
     */
    private $willBeSatisfied;

    /**
     * @param bool $willBeSatisfied
     */
    public function __construct($willBeSatisfied = true)
    {
        $this->willBeSatisfied = (bool) $willBeSatisfied;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return $this->willBeSatisfied;
    }

    public function and_(Specification $specification)
    {
        throw new \Exception('Not Implemented');
    }

    public function or_(Specification $specification)
    {
        throw new \Exception('Not Implemented');
    }

    public function not_()
    {
        throw new \Exception('Not Implemented');
    }
}
