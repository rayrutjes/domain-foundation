<?php

namespace spec\RayRutjes\DomainFoundation\Specification;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Specification\Specification;
use stdClass;

class NotSpecificationSpec extends ObjectBehavior
{
    public function let(Specification $specification)
    {
        $this->beConstructedWith($specification);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Specification\NotSpecification');
    }

    public function it_is_satisfied_if_the_specification_is_not_satisfied($specification)
    {
        $candidate = new stdClass();
        $specification->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(false);
        $this->isSatisfiedBy($candidate)->shouldReturn(true);
    }

    public function it_is_not_satisfied_if_the_specification_is_satisfied($specification)
    {
        $candidate = new stdClass();
        $specification->isSatisfiedBy($candidate)->shouldBeCalledTimes(1)->willReturn(true);
        $this->isSatisfiedBy($candidate)->shouldReturn(false);
    }
}
