<?php

namespace spec\RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRoot;
use RayRutjes\DomainFoundation\Domain\Event\EventRegistrationCallback;
use RayRutjes\DomainFoundation\EventBus\EventBus;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\UnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\SaveAggregateCallback\SaveAggregateCallback;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultAggregateContainerSpec extends ObjectBehavior
{
    public function let(
        UnitOfWork $unitOfWork,
        UnitOfWorkEventRegistrationCallbackFactory $eventRegistrationCallbackFactory,
        AggregateRoot $aggregateRoot1,
        AggregateRoot $aggregateRoot2,
        EventBus $eventBus,
        SaveAggregateCallback $saveAggregateCallback,
        EventRegistrationCallback $eventRegistrationCallback
    ) {
        $eventRegistrationCallbackFactory->create(Argument::any(), Argument::any())->willReturn($eventRegistrationCallback);

        $this->beConstructedWith($unitOfWork, $eventRegistrationCallbackFactory);
        $this->add($aggregateRoot1, $eventBus, $saveAggregateCallback);
        $this->add($aggregateRoot2, $eventBus, $saveAggregateCallback);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\DefaultAggregateContainer');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\AggregateContainer');
    }

    public function it_can_return_all_registered_aggregate_roots($aggregateRoot1, $aggregateRoot2)
    {
        $this->all()->shouldReturn([$aggregateRoot1, $aggregateRoot2]);
    }

    public function it_can_trigger_all_save_callbacks_on_the_registered_aggregate_roots($aggregateRoot1, $aggregateRoot2, $saveAggregateCallback)
    {
        $saveAggregateCallback->save($aggregateRoot1)->shouldBeCalledTimes(1);
        $saveAggregateCallback->save($aggregateRoot2)->shouldBeCalledTimes(1);
        $this->saveAggregateRoots();
    }

    public function it_can_clear_all_registered_aggregate_roots_and_corresponding_save_callbacks()
    {
        $this->clear();
        $this->all()->shouldReturn([]);
    }

    public function it_can_register_an_aggregate_root_and_return_it(AggregateRoot $aggregateRoot, $eventRegistrationCallback, $eventBus, $saveAggregateCallback)
    {
        $aggregateRoot->addEventRegistrationCallback($eventRegistrationCallback)->shouldBeCalled();
        $this->add($aggregateRoot, $eventBus, $saveAggregateCallback)->shouldReturn($aggregateRoot);
    }

    public function it_should_return_a_previously_registered_similar_aggregate_root_if_available(AggregateRoot $aggregateRoot, $aggregateRoot1, $eventBus, $saveAggregateCallback)
    {
        $aggregateRoot1->sameIdentityAs($aggregateRoot)->willReturn(true);
        $aggregateRoot->addEventRegistrationCallback(Argument::any())->shouldNotBeCalled();
        $this->add($aggregateRoot, $eventBus, $saveAggregateCallback)->shouldReturn($aggregateRoot1);
        $this->all()->shouldHaveCount(2);
    }
}
