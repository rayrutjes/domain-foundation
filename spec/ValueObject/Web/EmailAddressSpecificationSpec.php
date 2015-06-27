<?php

namespace spec\RayRutjes\DomainFoundation\ValueObject\Web;

use PhpSpec\ObjectBehavior;

class EmailAddressSpecificationSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\ValueObject\Web\EmailAddressSpecification');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Specification\Specification');
    }

    public function it_is_only_satisfied_by_a_string()
    {
        $this->isSatisfiedBy(null)->shouldReturn(false);
        $this->isSatisfiedBy(10)->shouldReturn(false);
        $this->isSatisfiedBy('test@example.com')->shouldReturn(true);
    }

    public function it_is_only_satisfied_with_a_well_formatted_email_address()
    {
        $this->isSatisfiedBy('')->shouldReturn(false);
        $this->isSatisfiedBy('test[@]example.com')->shouldReturn(false);
        $this->isSatisfiedBy('test@example.com')->shouldReturn(true);
    }
}
