<?php

namespace RayRutjes\DomainFoundation\Test\Domain\Event\Stream;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;

class GenericEventStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresAStreamOnlyContainsEvents()
    {
        new GenericEventStream([new \stdClass()]);
    }

    /**
     * @dataProvider streamProvider
     */
    public function testCanTellIfTheEndOfTheStreamHasBeenReached(GenericEventStream $stream, $numberOfEvents)
    {
        // Should return true until the end of the stream has been reached.
        for ($i = 0; $i < $numberOfEvents; $i++) {
            $this->assertTrue($stream->hasNext());
            $stream->next();
        }
        $this->assertFalse($stream->hasNext());
    }

    /**
     * @dataProvider streamProvider
     */
    public function testCanReturnTheCurrentEventAndMoveTheStreamCursorForward(GenericEventStream $stream, $numberOfEvents)
    {
        for ($i = 0; $i < $numberOfEvents; $i++) {
            $this->assertEquals($i, $stream->next()->index);
        }
    }

    /**
     * @dataProvider streamProvider
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsAnExceptionIfTryingToGetNonExistingNextEvent(GenericEventStream $stream, $numberOfEvents)
    {
        for ($i = 0; $i < $numberOfEvents; $i++) {
            $stream->next();
        }
        $stream->next();
    }

    /**
     * @dataProvider streamProvider
     */
    public function testCanPeekTheCurrentEvent(GenericEventStream $stream, $numberOfEvents)
    {
        if (0 === $numberOfEvents) {
            return;
        }

        for ($i = 0; $i < $numberOfEvents; $i++) {
            $this->assertEquals($i, $stream->peek()->index);
            $stream->next();
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsAnExceptionIfTryingToPeekNonExistingNextEvent()
    {
        $stream = new GenericEventStream([]);
        $stream->peek();
    }

    public function testCanTellIfTheStreamIsEmpty()
    {
        $stream = new GenericEventStream([]);
        $this->assertTrue($stream->isEmpty());
    }

    public function testCanTellIfTheStreamIsNotEmpty()
    {
        $stream = new GenericEventStream([new EventStub(0)]);
        $this->assertFalse($stream->isEmpty());
    }

    public function streamProvider()
    {
        return [
            [$this->provideStreamWithStubbedEvents(0), 0],
            [$this->provideStreamWithStubbedEvents(1), 1],
            [$this->provideStreamWithStubbedEvents(3), 3],
        ];
    }

    public function provideStreamWithStubbedEvents($numberOfEvents)
    {
        $events = [];

        for ($i = 0; $i < $numberOfEvents; $i++) {
            $events[] = new EventStub($i);
        }

        return new GenericEventStream($events);
    }
}

class EventStub implements Event
{
    public $index;

    public function __construct($index)
    {
        $this->index = $index;
    }

    public function aggregateRootIdentifier()
    {
    }

    public function sequenceNumber()
    {
    }

    public function identifier()
    {
    }

    public function payload()
    {
    }

    public function payloadType()
    {
    }

    public function metadata()
    {
    }

    public function metadataType()
    {
    }

    public function enrichMetadata(array $data)
    {
    }
}
