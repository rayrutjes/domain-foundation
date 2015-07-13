<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Specification;

use RayRutjes\DomainFoundation\Specification\NotSpecification;

class NotSpecificationTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSatisfiedIfTheSpecificationIsNot()
    {
        $specification = new SpecificationStub(false);
        $notSpecification = new NotSpecification($specification);

        $this->assertTrue($notSpecification->isSatisfiedBy(new \stdClass()));
    }

    public function testIsNotSatisfiedIfTheSpecificationIs()
    {
        $specification = new SpecificationStub(true);
        $notSpecification = new NotSpecification($specification);

        $this->assertFalse($notSpecification->isSatisfiedBy(new \stdClass()));
    }
}
