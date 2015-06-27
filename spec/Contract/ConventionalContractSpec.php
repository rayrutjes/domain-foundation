<?php

namespace spec\RayRutjes\DomainFoundation\Contract;

use PhpSpec\ObjectBehavior;
use RayRutjes\DomainFoundation\Contract\Contract;

class ConventionalContractSpec extends ObjectBehavior
{
    private $className = 'spec\RayRutjes\DomainFoundation\Contract\ConventionalContractSpec';

    public function let()
    {
        $this->beConstructedWith($this->className);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Contract\ConventionalContract');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Contract\Contract');
    }

    public function it_can_only_be_initialized_with_a_string()
    {
        $this->shouldThrow(new \InvalidArgumentException('The class name should be a string.'))->during('__construct', [null]);
        $this->shouldThrow(new \InvalidArgumentException('The class name should be a string.'))->during('__construct', [1]);
        $this->shouldThrow(new \InvalidArgumentException('The class name should be a string.'))->during('__construct', [new \stdClass()]);
    }

    public function it_can_return_its_class_name()
    {
        $this->className()->shouldReturn($this->className);
    }

    public function it_can_be_translated_into_a_string()
    {
        $this->toString()->shouldReturn($this->className);
        $this->__toString()->shouldReturn($this->className);
    }

    public function it_can_be_compared_with_another_contract(Contract $contract)
    {
        $contract->toString()->shouldBeCalledTimes(2);

        $contract->toString()->willReturn('stdClass');
        $this->equals($contract)->shouldReturn(false);

        $contract->toString()->willReturn($this->className);
        $this->equals($contract)->shouldReturn(true);
    }
}
