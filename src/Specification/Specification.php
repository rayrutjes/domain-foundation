<?php

namespace RayRutjes\DomainFoundation\Specification;

interface Specification
{
    /**
     * @param $object
     *
     * @return bool
     */
    public function isSatisfiedBy($object);

    /**
     * The method has an underscore because 'and' is reserved.
     *
     * @param Specification $specification
     *
     * @return AndSpecification
     */
    public function and_(Specification $specification);

    /**
     * The method has an underscore because 'or' is reserved.
     *
     * @param Specification $specification
     *
     * @return OrSpecification
     */
    public function or_(Specification $specification);

    /**
     * The method has an underscore for consistency with 'and_' and 'or_'.
     *
     * @return NotSpecification
     */
    public function not_();
}
