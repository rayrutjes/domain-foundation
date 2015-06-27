<?php

namespace spec\RayRutjes\DomainFoundation\ValueObject\Web;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\ValueObject\ValueObject;
use RayRutjes\DomainFoundation\ValueObject\Web\EmailAddress;

class EmailAddressSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('g.mansoif@example.com');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\ValueObject\Web\EmailAddress');
        $this->shouldHaveType('RayRutjes\DomainFoundation\ValueObject\ValueObject');
    }

    public function it_should_not_accept_a_misformatted_email_address()
    {
        $this->shouldThrow(new InvalidArgumentException('EmailAddress expects a well formatted email address.'))->during('__construct', ['g.mansoif[@]example.com']);
        $this->shouldThrow(new InvalidArgumentException('EmailAddress expects a well formatted email address.'))->during('__construct', [null]);
    }

    public function it_can_be_compared_with_another_email_address(ValueObject $valueObject)
    {
        $this->sameValueAs($valueObject)->shouldReturn(false);

        $same = new EmailAddress('g.mansoif@example.com');
        $this->sameValueAs($same)->shouldReturn(true);

        $other = new EmailAddress('other@example.com');
        $this->sameValueAs($other)->shouldReturn(false);
    }

    public function it_can_be_translated_to_a_string()
    {
        $this->toString()->shouldReturn('g.mansoif@example.com');
        $this->__toString()->shouldReturn('g.mansoif@example.com');
    }
}
