<?php

namespace RayRutjes\DomainFoundation\Test\Unit\UnitOfWork;

use RayRutjes\DomainFoundation\EventBus\SimpleEventBus;
use RayRutjes\DomainFoundation\Test\Unit\Domain\Event\Stream\EventStub;
use RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWorkEventRegistrationCallback;

class DefaultUnitOfWorkEventRegistrationCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DefaultUnitOfWorkEventRegistrationCallback
     */
    private $callback;
    private $unitOfWork;
    private $eventBus;

    public function setUp()
    {
        $this->unitOfWork = $this->unitOfWorkMock();
        $this->eventBus = $this->eventBus();

        $this->callback = new DefaultUnitOfWorkEventRegistrationCallback($this->unitOfWork, $this->eventBus);
    }

    public function testShouldPublishEventThroughTheUnitOfWorkOnEventRegistration()
    {
        $event = $this->event();

        $this->unitOfWork->expects($this->once())
            ->method('publishEvent')
            ->with($this->identicalTo($event), $this->identicalTo($this->eventBus));

        $this->callback->onEventRegistration($event);
    }

    public function testShouldInvokeUnitOfWorkEventRegistrationListenersOnEventRegistration()
    {
        $event = $this->event();

        $this->unitOfWork->expects($this->once())
            ->method('invokeEventRegistrationListeners')
            ->with($this->identicalTo($event))
            ->willReturn($event);

        $this->assertSame($event, $this->callback->onEventRegistration($event));
    }

    private function event()
    {
        return new EventStub(0);
    }

    private function eventBus()
    {
        return new SimpleEventBus();
    }

    private function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }
}
