<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Repository;

use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Repository\ConflictingChangesException;

class ConflictingChangesExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConflictingChangesException
     */
    private $exception;

    private $conflictingStream;
    private $committedStream;

    public function setUp()
    {
        $this->conflictingStream = new GenericEventStream();
        $this->committedStream = new GenericEventStream();

        $this->exception = new ConflictingChangesException($this->conflictingStream, $this->committedStream);
    }

    public function testCanRetrieveTheConflictingEventStream()
    {
        $this->assertSame($this->conflictingStream, $this->exception->conflictingEventStream());
    }

    public function testCanRetrieveTheCommittedEventStream()
    {
        $this->assertSame($this->committedStream, $this->exception->committedEventStream());
    }
}
