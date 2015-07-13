<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Specification;

use RayRutjes\DomainFoundation\Specification\AndSpecification;

class AndSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedIfBothSpecificationsAre()
    {
        $left = new SpecificationStub(true);
        $right = new SpecificationStub(true);
        $specification = new AndSpecification($left, $right);

        $this->assertTrue($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsNotSatisfiedIfLeftSpecificationIsNot()
    {
        $left = new SpecificationStub(false);
        $right = new SpecificationStub(true);
        $specification = new AndSpecification($left, $right);

        $this->assertFalse($specification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsNotSatisfiedIfRightSpecificationIsNot()
    {
        $left = new SpecificationStub(true);
        $right = new SpecificationStub(false);
        $specification = new AndSpecification($left, $right);

        $this->assertFalse($specification->isSatisfiedBy(new \stdClass()));
    }
}
