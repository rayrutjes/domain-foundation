<?php

namespace spec\RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

use PhpSpec\ObjectBehavior;

class AbstractQuerySpec extends ObjectBehavior
{
    public function let(\PDO $pdo)
    {
        $this->beAnInstanceOf('RayRutjes\DomainFoundation\Stub\Persistence\Pdo\EventStore\Query\AbstractQueryStub');
        $this->beConstructedWith($pdo, 'tablename', 'sql');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\AbstractQuery');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\PdoEventStoreQuery');
    }

    public function it_should_ensure_the_table_name_is_valid(\PDO $pdo)
    {
        $this->shouldThrow(new \LogicException('Table name must be a string.'))->during('__construct', [$pdo, null, 'string']);
        $this->shouldThrow(new \LogicException('Table name must be a string.'))->during('__construct', [$pdo, 33, 'string']);
    }

    public function it_should_ensure_sql_is_valid(\PDO $pdo)
    {
        $this->shouldThrow(new \LogicException('Sql must be a string.'))->during('__construct', [$pdo, 'tablename', null]);
        $this->shouldThrow(new \LogicException('Sql must be a string.'))->during('__construct', [$pdo, 'tablename', 33]);
    }
}
