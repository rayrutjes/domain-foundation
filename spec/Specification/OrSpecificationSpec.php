<?php

namespace spec\RayRutjes\DomainFoundation\Specification;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Specification\Specification;
use stdClass;

class OrSpecificationSpec extends ObjectBehavior
{
    public function let(Specification $spec1, Specification $spec2)
    {
        $this->beConstructedWith($spec1, $spec2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Specification\OrSpecification');
    }

    public function it_is_satisfied_if_at_least_one_of_the_specification_is_satisfied($spec1, $spec2)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->shouldBeCalled()->willReturn(true);
        $spec2->isSatisfiedBy($candidate)->shouldBeCalled()->willReturn(false);
        $this->isSatisfiedBy($candidate)->shouldReturn(true);

        $spec1->isSatisfiedBy($candidate)->willReturn(false);
        $spec2->isSatisfiedBy($candidate)->willReturn(true);
        $this->isSatisfiedBy($candidate)->shouldReturn(true);
    }

    public function it_is_not_satisfied_if_all_specifications_are_unsatisfied($spec1, $spec2)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(false);
        $spec2->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(false);
        $this->isSatisfiedBy($candidate)->shouldReturn(false);
    }
}
