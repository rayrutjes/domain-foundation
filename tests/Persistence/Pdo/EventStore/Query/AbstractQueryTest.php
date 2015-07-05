<?php

namespace RayRutjes\DomainFoundation\Test\Persistence\Pdo\EventStore\Query;

class AbstractQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheTableNameIsAString()
    {
        $this->getMockForAbstractClass('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\AbstractQuery', [
            $this->stubbedPdo(),
            3,
            'sql',
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresTheSqlIsAString()
    {
        $this->getMockForAbstractClass('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\AbstractQuery', [
            $this->stubbedPdo(),
            'tablename',
            3,
        ]);
    }

    private function stubbedPdo()
    {
        return $this->getMockBuilder('PDO')
                    ->disableOriginalConstructor()
                    ->getMock();
    }
}
