<?php

namespace RayRutjes\DomainFoundation\Test\UnitOfWork\Listener;

use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerAdapter;

class UnitOfWorkListenerAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function testMakesCreatingUnitOfWorkListenersEasy()
    {
        $exception = new \Exception();
        $unitOfWork = $this->unitOfWorkMock();
        $event = $this->eventMock();

        $adapter = new DummyUnitOfWorkListenerAdapter();
        $adapter->afterCommit($unitOfWork);
        $adapter->onRollback($unitOfWork, $exception);
        $this->assertSame($event, $adapter->onEventRegistration($unitOfWork, $event));
        $adapter->onPrepareCommit($unitOfWork, [], []);
        $adapter->onPrepareTransactionCommit($unitOfWork);
        $adapter->onCleanup($unitOfWork);
    }

    private function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }

    private function eventMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Domain\Event\Event');
    }
}

class DummyUnitOfWorkListenerAdapter extends UnitOfWorkListenerAdapter
{
}
