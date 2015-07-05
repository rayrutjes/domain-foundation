<?php

namespace RayRutjes\DomainFoundation\Test\Repository;

use RayRutjes\DomainFoundation\Repository\SimpleSaveAggregateCallback;
use RayRutjes\DomainFoundation\Test\Domain\AggregateRoot\AggregateRootIdentifierStub;
use RayRutjes\DomainFoundation\Test\Domain\AggregateRoot\EventSourcedAggregateRootStub;

class SimpleSaveAggregateCallbackTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldTriggerTheSaveOnTheRepository()
    {
        $aggregateRoot = EventSourcedAggregateRootStub::create(new AggregateRootIdentifierStub('identifier'));

        $repository = $this->getMock('RayRutjes\DomainFoundation\Repository\Repository');

        $repository->expects($this->once())
            ->method('doSave')
            ->with($this->identicalTo($aggregateRoot))
        ;

        $callback = new SimpleSaveAggregateCallback($repository);

        $callback->save($aggregateRoot);
    }

    public function testShouldCommitTheAggregateRootChanges()
    {
        // This is kind of a functional test though.
        $aggregateRoot = EventSourcedAggregateRootStub::create(new AggregateRootIdentifierStub('identifier'));

        $repository = $this->getMock('RayRutjes\DomainFoundation\Repository\Repository');

        $callback = new SimpleSaveAggregateCallback($repository);

        $callback->save($aggregateRoot);

        $this->assertTrue($aggregateRoot->uncommittedChanges()->isEmpty());
    }
}
