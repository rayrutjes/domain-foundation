<?php

namespace integration\Persistence\Test\Integration\Pdo\EventStore;

use PHPUnit_Extensions_Database_DataSet_IDataSet;
use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoEventStore;
use RayRutjes\DomainFoundation\Test\Resources\DatabaseTestCase;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\AggregateRootIdentifierStub;
use RayRutjes\DomainFoundation\Test\Unit\Message\PayloadStub;
use Rhumsaa\Uuid\Uuid;

class PdoEventStoreTest extends DatabaseTestCase
{
    /**
     * @var PdoEventStore
     */
    private $eventStore;

    public function setUp()
    {
        parent::setUp();

        $this->eventStore = new PdoEventStore($this->getConnection()->getConnection());
    }


    /**
     * @expectedException \RayRutjes\DomainFoundation\Repository\ConcurrencyException
     */
    public function testDetectsConcurrencyAndThrowsTheAccordingException()
    {
        $aggregateRootIdentifier = new AggregateRootIdentifierStub(Uuid::NIL);
        $aggregateRootType = Contract::createFromClassName('Product');
        $messageIdentifier = new MessageIdentifier(UUID::NIL);
        $payload = new PayloadStub();

        $event = new GenericEvent($aggregateRootIdentifier, 1, $messageIdentifier, $payload);
        $eventStream = new GenericEventStream([$event]);

        $this->eventStore->append($aggregateRootType, $eventStream);
    }



    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return $this->createArrayDataSet([
            'events' =>[
                [
                    'aggregate_id'   => Uuid::NIL,
                    'aggregate_type' => 'Product',
                    'aggregate_version' => 1,
                    'event_id' => Uuid::NIL,
                    'event_payload' => '{}',
                    'event_payload_type' => '',
                    'event_metadata' => '{}',
                    'event_metadata_type' => '',
                ]
            ]
        ]);
    }
}
