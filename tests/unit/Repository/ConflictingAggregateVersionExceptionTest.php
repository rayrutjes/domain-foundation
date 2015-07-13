<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Repository;

use RayRutjes\DomainFoundation\Repository\ConflictingAggregateVersionException;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\AggregateRootIdentifierStub;

class ConflictingAggregateVersionExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConflictingAggregateVersionException
     */
    private $exception;

    private $aggregateRootIdentifier;

    public function setUp()
    {
        $this->aggregateRootIdentifier = new AggregateRootIdentifierStub('identifier');
        $this->exception = new ConflictingAggregateVersionException($this->aggregateRootIdentifier, 2, 3);
    }

    public function testCanRetrieveTheAggregateRootIdentifier()
    {
        $this->assertSame($this->aggregateRootIdentifier, $this->exception->aggregateRootIdentifier());
    }

    public function testCanRetrieveTheExpectedVersion()
    {
        $this->assertEquals(2, $this->exception->expectedVersion());
    }

    public function testCanRetrieveTheActualVersion()
    {
        $this->assertEquals(3, $this->exception->actualVersion());
    }
}
