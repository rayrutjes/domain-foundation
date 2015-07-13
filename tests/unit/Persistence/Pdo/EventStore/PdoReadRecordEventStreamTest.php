<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoReadRecordEventStream;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\AggregateRootIdentifierStub;
use RayRutjes\DomainFoundation\Test\Unit\Message\PayloadStub;
use Rhumsaa\Uuid\Uuid;

class PdoReadRecordEventStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider streamProvider
     */
    public function testCanTellIfTheEndOfTheStreamHasBeenReached(PdoReadRecordEventStream $stream, $numberOfEvents)
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
    public function testCanReturnTheCurrentEventAndMoveTheStreamCursorForward(PdoReadRecordEventStream $stream, $numberOfEvents)
    {
        for ($i = 0; $i < $numberOfEvents; $i++) {
            $this->assertEquals($i + 1, $stream->next()->sequenceNumber());
        }
    }

    /**
     * @dataProvider streamProvider
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsAnExceptionIfTryingToGetNonExistingNextEvent(PdoReadRecordEventStream $stream, $numberOfEvents)
    {
        for ($i = 0; $i < $numberOfEvents; $i++) {
            $stream->next();
        }
        $stream->next();
    }

    /**
     * @dataProvider streamProvider
     */
    public function testCanPeekTheCurrentEvent(PdoReadRecordEventStream $stream, $numberOfEvents)
    {
        if (0 === $numberOfEvents) {
            return;
        }

        for ($i = 0; $i < $numberOfEvents; $i++) {
            $this->assertEquals($i + 1, $stream->peek()->sequenceNumber());
            $stream->next();
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testThrowsAnExceptionIfTryingToPeekNonExistingNextEvent()
    {
        $stream = $this->provideStreamWithStubbedRecords(0);
        $stream->peek();
    }

    public function testCanTellIfTheStreamIsEmpty()
    {
        $stream = $this->provideStreamWithStubbedRecords(0);
        $this->assertTrue($stream->isEmpty());
    }

    public function testCanTellIfTheStreamIsNotEmpty()
    {
        $stream = $this->provideStreamWithStubbedRecords(1);
        $this->assertFalse($stream->isEmpty());
    }

    public function streamProvider()
    {
        return [
            [$this->provideStreamWithStubbedRecords(0), 0],
            [$this->provideStreamWithStubbedRecords(1), 1],
            [$this->provideStreamWithStubbedRecords(3), 3],
        ];
    }

    public function provideStreamWithStubbedRecords($numberOfRecords)
    {
        $aggregateRootIdentifier = new AggregateRootIdentifierStub();
        $records = $this->provideStubbedRecords($numberOfRecords);

        $eventSerializer = $this->getMock('RayRutjes\DomainFoundation\Domain\Event\Serializer\EventSerializer');
        $eventSerializer->method('deserializePayload')->willReturn(new PayloadStub());
        $eventSerializer->method('deserializeMetadata')->willReturn(new Metadata());

        return new PdoReadRecordEventStream($aggregateRootIdentifier, $records, $eventSerializer);
    }

    public function provideStubbedRecords($numberOfRecords)
    {
        $records = [];
        for ($i = 0; $i < $numberOfRecords; $i++) {
            $records[] = [
                'event_id' => Uuid::NIL,
                'event_payload' => '{}',
                'event_payload_type' => 'Serializable',
                'event_metadata' => '{}',
                'event_metadata_type' => 'RayRutjes\DomainFoundation\Message\Metadata',
                'aggregate_version' => $i + 1,
            ];
        }

        return $records;
    }
}
