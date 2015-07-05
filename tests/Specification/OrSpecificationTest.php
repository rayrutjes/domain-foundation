<?php

namespace RayRutjes\DomainFoundation\Test\Specification;

use RayRutjes\DomainFoundation\Specification\OrSpecification;

class OrSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedIfBothSpecificationsAre()
    {
        $left = new SpecificationStub(true);
        $right = new SpecificationStub(true);
        $specification = new OrSpecification($left, $right);

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsSatisfiedIfLeftSpecificationIs()
    {
        $left = new SpecificationStub(true);
        $right = new SpecificationStub(false);
        $specification = new OrSpecification($left, $right);

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsSatisfiedIfRightSpecificationIs()
    {
        $left = new SpecificationStub(false);
        $right = new SpecificationStub(true);
        $specification = new OrSpecification($left, $right);

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsNotSatisfiedIfBothSpecificationsAreNot()
    {
        $left = new SpecificationStub(false);
        $right = new SpecificationStub(false);
        $specification = new OrSpecification($left, $right);

        $this->assertFalse($specification->isSatisfiedBy(new \stdClass()));
    }
}
