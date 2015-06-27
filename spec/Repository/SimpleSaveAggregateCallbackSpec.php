<?php

namespace spec\RayRutjes\DomainFoundation\Repository;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Repository\AggregateRootRepository;

class SimpleSaveAggregateCallbackSpec extends ObjectBehavior
{
    public function let(AggregateRootRepository $aggregateRootRepository)
    {
        $this->beConstructedWith($aggregateRootRepository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Repository\SimpleSaveAggregateCallback');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback');
    }

    public function it_handles_the_aggregate_save_behaviour($aggregateRootRepository, AggregateRoot $aggregateRoot)
    {
        $aggregateRootRepository->doSave($aggregateRoot)->shouldBeCalledTimes(1);
        $aggregateRoot->commitChanges()->shouldBeCalled();

        $this->save($aggregateRoot);
    }
}
