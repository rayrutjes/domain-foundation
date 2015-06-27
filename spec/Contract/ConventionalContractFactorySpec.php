<?php

namespace spec\RayRutjes\DomainFoundation\Contract;

use PhpSpec\ObjectBehavior;

class ConventionalContractFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Contract\ConventionalContractFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Contract\ContractFactory');
    }

    public function it_can_create_a_contract_from_a_class_name()
    {
        $this->createFromClassName('stdClass')->shouldHaveType('RayRutjes\DomainFoundation\Contract\ConventionalContract');
    }

    public function it_can_create_a_contract_from_an_object()
    {
        $this->createFromObject(new \stdClass())->shouldHaveType('RayRutjes\DomainFoundation\Contract\ConventionalContract');

        $this->shouldThrow(new \InvalidArgumentException('The object should be an object.'))->during('createFromObject', [null]);
        $this->shouldThrow(new \InvalidArgumentException('The object should be an object.'))->during('createFromObject', [1]);
        $this->shouldThrow(new \InvalidArgumentException('The object should be an object.'))->during('createFromObject', ['object']);
    }
}
