<?php

namespace RayRutjes\DomainFoundation\Test\Specification;

use RayRutjes\DomainFoundation\Specification\AbstractSpecification;

class AbstractSpecificationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractSpecification
     */
    private $specification;

    public function setUp()
    {
        $this->specification = new AbstractSpecificationStub();
    }

    public function testCanBeMadePartOfAnAndSpecification()
    {
        $stub = $this->getMock('RayRutjes\DomainFoundation\Specification\Specification');
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Specification\AndSpecification', $this->specification->and_($stub));
    }

    public function testCanBeMadePartOfAnOrSpecification()
    {
        $stub = $this->getMock('RayRutjes\DomainFoundation\Specification\Specification');
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Specification\OrSpecification', $this->specification->or_($stub));
    }

    public function testCanNegateASpecification()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Specification\NotSpecification', $this->specification->not_());
    }
}

class AbstractSpecificationStub extends AbstractSpecification
{
    public function isSatisfiedBy($object)
    {
        throw new \Exception('Not implemented in stub.');
    }
}
