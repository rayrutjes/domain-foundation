<?php

namespace spec\RayRutjes\DomainFoundation\ValueObject\Identity;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\ValueObject\ValueObject;
use Rhumsaa\Uuid\Uuid;

class UuidSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(Uuid::NIL);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\ValueObject\Identity\Uuid');
        $this->shouldHaveType('RayRutjes\DomainFoundation\ValueObject\ValueObject');
    }

    public function it_should_not_accept_an_invalid_uuid()
    {
        $this->shouldThrow(new InvalidArgumentException('Uuid expected a string.'))->during('__construct', [null]);
        $this->shouldThrow(new InvalidArgumentException('Invalid Uuid format.'))->during('__construct', ['not a valid uuid string']);
    }

    public function it_can_be_generated()
    {
        $this->beConstructedThrough('generate', []);
    }

    public function it_can_be_compared_with_another_uuid(ValueObject $valueObject)
    {
        $this->sameValueAs($valueObject)->shouldReturn(false);

        $nilUuid = new \RayRutjes\DomainFoundation\ValueObject\Identity\Uuid(Uuid::NIL);
        $this->sameValueAs($nilUuid)->shouldReturn(true);

        $randomUuid = \RayRutjes\DomainFoundation\ValueObject\Identity\Uuid::generate();
        $this->sameValueAs($randomUuid)->shouldReturn(false);
    }

    public function it_can_be_translated_to_a_string()
    {
        $this->toString()->shouldReturn(Uuid::NIL);
        $this->__toString()->shouldReturn(Uuid::NIL);
    }
}
