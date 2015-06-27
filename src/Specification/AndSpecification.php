<?php

namespace RayRutjes\DomainFoundation\Specification;

class AndSpecification extends AbstractSpecification implements Specification
{
    /**
     * @var Specification
     */
    private $left;

    /**
     * @var Specification
     */
    private $right;

    /**
     * @param Specification $left
     * @param Specification $right
     */
    public function __construct(Specification $left, Specification $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object)
    {
        return $this->left->isSatisfiedBy($object) && $this->right->isSatisfiedBy($object);
    }
}
