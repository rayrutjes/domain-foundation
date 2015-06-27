<?php

namespace spec\RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class InsertQuerySpec extends ObjectBehavior
{
    public function let(\PDO $pdo)
    {
        $this->beConstructedWith($pdo, 'events', 'sql');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\InsertQuery');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\AbstractQuery');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\PdoEventStoreQuery');
    }

    public function it_can_return_the_pdo_prepared_statement($pdo, \PDOStatement $statement)
    {
        $pdo->prepare(Argument::any())->shouldBeCalledTimes(1)->willReturn($statement);
        $this->prepare()->shouldReturn($statement);
    }
}
