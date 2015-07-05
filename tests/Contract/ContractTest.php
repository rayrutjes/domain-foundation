<?php

/**
 * Created by PhpStorm.
 * User: raymond
 * Date: 05/07/15
 * Time: 23:31.
 */
namespace RayRutjes\DomainFoundation\Test\Contract;

use RayRutjes\DomainFoundation\Contract\Contract;

class ContractTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeCreatedFromAClassName()
    {
        $contract = Contract::createFromClassName('\stdClass');
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $contract);

        return $contract;
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheClassNameIsAString()
    {
        Contract::createFromClassName(null);
    }

    public function testCanBeCreatedFromAnObject()
    {
        $contract = Contract::createFromObject(new \stdClass());
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Contract\Contract', $contract);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheObjectIsAnObject()
    {
        Contract::createFromObject('\stdClass');
    }

    /**
     * @depends testCanBeCreatedFromAClassName
     */
    public function testCanBeTranslatedToAString(Contract $contract)
    {
        $this->assertEquals('\stdClass', $contract->toString());
    }

    /**
     * @depends testCanBeCreatedFromAClassName
     */
    public function testCanBeCastedToAString(Contract $contract)
    {
        $this->assertEquals('\stdClass', (string) $contract);
    }

    /**
     * @depends testCanBeCreatedFromAClassName
     */
    public function testCanDetermineIfItIsEqualToAnotherContract(Contract $contract)
    {
        $this->assertTrue($contract->equals($contract));
        $this->assertFalse($contract->equals(Contract::createFromClassName('AnotherClassName')));
    }
}
