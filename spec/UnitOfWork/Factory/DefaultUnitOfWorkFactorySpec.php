<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\Factory;

use PhpSpec\ObjectBehavior;

class DefaultUnitOfWorkFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Factory\DefaultUnitOfWorkFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\Factory\UnitOfWorkFactory');
    }

    public function it_can_create_a_default_unit_of_work()
    {
        $this->createUnitOfWork()->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWork');
    }
}
