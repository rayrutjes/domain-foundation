<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory;

use PhpSpec\ObjectBehavior;

class DefaultStagingEventContainerFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory\DefaultStagingEventContainerFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory\StagingEventContainerFactory');
    }

    public function it_can_create_a_default_staging_event_container()
    {
        $this->create()->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\DefaultStagingEventContainer');
    }
}
