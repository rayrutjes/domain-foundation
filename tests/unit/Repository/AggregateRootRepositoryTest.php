<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Repository;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\GenericEvent;
use RayRutjes\DomainFoundation\Domain\Event\Stream\GenericEventStream;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Repository\AggregateRootRepository;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\AggregateRootIdentifierStub;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\Created;
use RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\EventSourcedAggregateRootStub;

class AggregateRootRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AggregateRootRepository
     */
    private $repository;

    private $unitOfWork;

    /**
     * @var Contract
     */
    private $aggregateRootType;

    private $eventStore;

    private $eventBus;

    public function setUp()
    {
        $this->unitOfWork = $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
        $this->aggregateRootType = Contract::createFromClassName('RayRutjes\DomainFoundation\Test\Unit\Domain\AggregateRoot\EventSourcedAggregateRootStub');
        $this->eventStore = $this->getMock('RayRutjes\DomainFoundation\EventStore\EventStore');
        $this->eventBus = $this->getMock('RayRutjes\DomainFoundation\EventBus\EventBus');

        $this->repository = new AggregateRootRepository($this->unitOfWork, $this->aggregateRootType, $this->eventStore, $this->eventBus);
    }

    public function testMakeTheUnitOfWorkAwareWhenAnAggregateRootIsAdded()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::create(new AggregateRootIdentifierStub('identifier'));

        $this->unitOfWork->expects($this->once())
                         ->method('registerAggregate')
                         ->with(
                             $this->identicalTo($aggregateRoot),
                             $this->eventBus,
                             $this->isInstanceOf('RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback')
                         )
        ;

        $this->repository->add($aggregateRoot);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresOnlySupportedAggregateRootsCanBeAdded()
    {
        $aggregateRoot = $this->getMock('RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot');

        $this->repository->add($aggregateRoot);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresOnlyNewAggregateRootsCanBeAdded()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::create(new AggregateRootIdentifierStub('identifier'));

        $aggregateRoot->commitChanges();

        $this->repository->add($aggregateRoot);
    }

    public function testAppendAggregateRootChangesToTheEventStore()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::create(new AggregateRootIdentifierStub('identifier'));

        $this->eventStore->expects($this->once())
                         ->method('append')
                         ->with(
                             $this->identicalTo($this->aggregateRootType),
                             $this->isInstanceOf('RayRutjes\DomainFoundation\Domain\Event\Stream\EventStream')
                         )
        ;

        $this->repository->doSave($aggregateRoot);
    }

    /**
     * @expectedException \RayRutjes\DomainFoundation\Repository\AggregateNotFoundException
     */
    public function testShouldThrowAnExceptionIfAggregateRootWasNotFound()
    {
        $this->eventStore->method('read')->willReturn(new GenericEventStream());

        $this->repository->load(new AggregateRootIdentifierStub('identifier'));
    }

    public function testShouldMakeTheUnitOfWorkAwareOfTheLoadedAggregateRootAndReturnIt()
    {
        $identifier = new AggregateRootIdentifierStub('identifier');

        $event = new GenericEvent($identifier, 1, MessageIdentifier::generate(), new Created($identifier));

        $this->eventStore->method('read')->willReturn(new GenericEventStream([$event]));

        $this->unitOfWork->expects($this->once())
            ->method('registerAggregate')
            ->with(
                $this->isInstanceOf(EventSourcedAggregateRootStub::class),
                $this->eventBus,
                $this->isInstanceOf('RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback')
            )
            ->will($this->returnArgument(0))
        ;

        $this->assertInstanceOf(EventSourcedAggregateRootStub::class, $this->repository->load($identifier));
    }

    /**
     * @expectedException \RayRutjes\DomainFoundation\Repository\ConflictingAggregateVersionException
     */
    public function testShouldEnsureTheLoadedAggregateRootMatchesTheExpectedVersion()
    {
        $identifier = new AggregateRootIdentifierStub('identifier');

        $event = new GenericEvent($identifier, 1, MessageIdentifier::generate(), new Created($identifier));

        $this->eventStore->method('read')->willReturn(new GenericEventStream([$event]));

        $this->repository->load($identifier, 0);
    }
}
