<?php

namespace RayRutjes\DomainFoundation\Test\Specification;

use RayRutjes\DomainFoundation\Specification\CompositeSpecification;

class CompositeSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedIfAllSpecificationsAre()
    {
        $spec1 = new SpecificationStub(true);
        $spec2 = new SpecificationStub(true);
        $spec3 = new SpecificationStub(true);

        $specification = new CompositeSpecification($spec1, $spec2, $spec3);

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsNotSatisfiedIfOneOfTheSpecificationsIsNot()
    {
        $spec1 = new SpecificationStub(true);
        $spec2 = new SpecificationStub(false);
        $spec3 = new SpecificationStub(true);

        $specification = new CompositeSpecification($spec1, $spec2, $spec3);

        $this->assertFalse($specification->isSatisfiedBy(new \stdClass()));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCanOnlyBeACompositeOfSpecifications()
    {
        new CompositeSpecification(new \stdClass());
    }

    public function testIsSatisfiedWhenThereAreNoSpecifications()
    {
        $specification = new CompositeSpecification();

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }
}
