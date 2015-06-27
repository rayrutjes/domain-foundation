<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultUnitOfWorkEventRegistrationCallbackFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\DefaultUnitOfWorkEventRegistrationCallbackFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\UnitOfWorkEventRegistrationCallbackFactory');
    }

    public function it_can_create_a_default_unit_of_work_event_registration_callback(UnitOfWork $unitOfWork, EventBus $eventBus)
    {
        $this->create($unitOfWork, $eventBus)->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\DefaultUnitOfWorkEventRegistrationCallback');
    }
}
