<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Persistence\Pdo\EventStore;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoEventStore;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\AggregateRootIdentifierStub;
use RayRutjes\DomainFoundation\Test\Unit\Message\PayloadStub;

class PdoEventStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdoEventStore
     */
    private $eventStore;

    private $pdo;

    private $statement;

    public function setUp()
    {
        $this->statement = $this->getMockBuilder('PDOStatement')->getMock();

        $this->pdo = $this->getMockBuilder('PDO')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->pdo->method('prepare')
                  ->willReturn($this->statement);

        $this->eventStore = new PdoEventStore($this->pdo);
    }

    public function testAnEvenStreamCanBeAppend()
    {
        $aggregateRootType = Contract::createFromClassName('stdClass');
        $eventStream = $this->provideStubbedEventStream();

        $this->statement->expects($this->exactly(2))
                        ->method('execute');

        $this->eventStore->append($aggregateRootType, $eventStream);
    }

    /**
     * @expectedException \PDOException
     */
    public function testShouldRaiseThePdoExceptionIfInsertingFails()
    {
        $aggregateRootType = Contract::createFromClassName('stdClass');
        $eventStream = $this->provideStubbedEventStream();

        $pdoException = new \PDOException();
        $pdoException->errorInfo[0] = '0';
        $this->statement
            ->method('execute')
            ->will($this->throwException($pdoException));

        $this->eventStore->append($aggregateRootType, $eventStream);
    }

    /**
     * @expectedException \RayRutjes\DomainFoundation\Repository\ConcurrencyException
     */
    public function testShouldRaiseAConcurrencyExceptionIfConcurrencyConflictIsDetected()
    {
        $aggregateRootType = Contract::createFromClassName('stdClass');
        $eventStream = $this->provideStubbedEventStream();

        $pdoException = new \PDOException();
        $pdoException->errorInfo[0] = '23000';
        $this->statement
            ->method('execute')
            ->will($this->throwException($pdoException));

        $this->eventStore->append($aggregateRootType, $eventStream);
    }

    public function testRetrieveAllEventsForAnAggregateRoot()
    {
        $aggregateRootType = Contract::createFromClassName('stdClass');
        $aggregateRootIdentifier = new AggregateRootIdentifierStub();

        $this->statement->method('fetchAll')
                        ->willReturn([]);

        $this->assertInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream', $this->eventStore->read($aggregateRootType, $aggregateRootIdentifier));
    }

    public function testCanCreateTheEventStoreTable()
    {
        $this->statement->expects($this->once())
                        ->method('execute');

        $this->eventStore->createTable();
    }

    /**
     * @return GenericEventStream
     */
    private function provideStubbedEventStream()
    {
        $identifier = new AggregateRootIdentifierStub();
        $payload = new PayloadStub();

        $event1 = new GenericEvent($identifier, 1, MessageIdentifier::generate(), $payload);
        $event2 = new GenericEvent($identifier, 2, MessageIdentifier::generate(), $payload);

        return new GenericEventStream([$event1, $event2]);
    }
}
