<?php

namespace spec\RayRutjes\DomainFoundation\Specification;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Specification\Specification;
use stdClass;

class AndSpecificationSpec extends ObjectBehavior
{
    public function let(Specification $spec1, Specification $spec2)
    {
        $this->beConstructedWith($spec1, $spec2);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Specification\AndSpecification');
    }

    public function it_is_satisfied_if_both_specifications_are_satisfied($spec1, $spec2)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);
        $spec2->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);

        $this->isSatisfiedBy($candidate)->shouldReturn(true);
    }

    public function it_is_not_satisfied_if_one_of_the_specification_is_not_satisfied($spec1, $spec2)
    {
        $candidate = new stdClass();

        $spec1->isSatisfiedBy($candidate)->willReturn(true);
        $spec2->isSatisfiedBy($candidate)->willReturn(false);
        $this->isSatisfiedBy($candidate)->shouldReturn(false);

        $spec1->isSatisfiedBy($candidate)->willReturn(false);
        $spec2->isSatisfiedBy($candidate)->willReturn(true);
        $this->isSatisfiedBy($candidate)->shouldReturn(false);
    }
}
