<?php

namespace spec\RayRutjes\DomainFoundation\Specification;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Specification\Specification;
use stdClass;

class CompositeSpecificationSpec extends ObjectBehavior
{
    public function let(Specification $spec1, Specification $spec2, Specification $spec3)
    {
        $this->beConstructedWith($spec1, $spec2, $spec3);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Specification\CompositeSpecification');
    }

    public function it_is_initializable_with_an_unlimited_amount_of_specifications($spec1, $spec2, $spec3)
    {
        $this->beConstructedWith($spec1);
        $this->beConstructedWith($spec1, $spec2);
        $this->beConstructedWith($spec1, $spec2, $spec3);
    }

    public function it_can_only_be_initialized_with_specifications($spec1)
    {
        $object = new stdClass();

        $exception = new \InvalidArgumentException('RayRutjes\DomainFoundation\Specification expected');
        $this->shouldThrow($exception)->during('__construct', [$object]);
        $this->shouldThrow($exception)->during('__construct', [$spec1, $object]);
    }

    public function it_is_satisfied_if_all_specifications_are_satisfied($spec1, $spec2, $spec3)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);
        $spec2->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);
        $spec3->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);

        $this->isSatisfiedBy($candidate)->shouldReturn(true);
    }

    public function it_is_not_satisfied_if_at_least_one_of_the_specification_is_not_satisfied($spec1, $spec2, $spec3)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->willReturn(false);
        $spec2->isSatisfiedBy($candidate)->willReturn(false);
        $spec3->isSatisfiedBy($candidate)->willReturn(false);

        $this->isSatisfiedBy($candidate)->shouldReturn(false);

        $spec1->isSatisfiedBy($candidate)->willReturn(true);
        $spec2->isSatisfiedBy($candidate)->willReturn(false);
        $spec3->isSatisfiedBy($candidate)->willReturn(true);

        $this->isSatisfiedBy($candidate)->shouldReturn(false);
    }
}
