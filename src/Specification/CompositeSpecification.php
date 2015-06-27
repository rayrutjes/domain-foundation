<?php

namespace RayRutjes\DomainFoundation\Specification;

/**
 * Works like AndSpecification, only here you can pass an unlimited amount of
 * Specifications in the constructor.
 */
class CompositeSpecification extends AbstractSpecification implements Specification
{
    /**
     * @var array
     */
    private $specifications = [];

    public function __construct()
    {
        foreach (func_get_args() as $specification) {
            if (!($specification instanceof Specification)) {
                throw new \InvalidArgumentException('RayRutjes\DomainFoundation\Specification expected');
            }
            $this->specifications[] = $specification;
        }
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($object)) {
                return false;
            }
        }

        return true;
    }
}
