<?php

namespace RayRutjes\DomainFoundation\Specification;

class NotSpecification extends AbstractSpecification implements Specification
{
    /**
     * @var Specification
     */
    private $specification;

    /**
     * @param Specification $specification
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return !$this->specification->isSatisfiedBy($object);
    }
}
