<?php

namespace spec\RayRutjes\DomainFoundation\Persistence\Pdo\UnitOfWork;

use PhpSpec\ObjectBehavior;

class PdoTransactionManagerSpec extends ObjectBehavior
{
    public function let(\PDO $pdo)
    {
        $this->beConstructedWith($pdo);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Persistence\Pdo\UnitOfWork\PdoTransactionManager');
        $this->shouldHaveType('RayRutjes\DomainFoundation\UnitOfWork\TransactionManager');
    }

    public function it_can_start_a_transaction($pdo)
    {
        $pdo->inTransaction()->willReturn(false);
        $pdo->beginTransaction()->shouldBeCalledTimes(1);
        $this->startTransaction();
    }

    public function it_should_disallow_nested_transactions($pdo)
    {
        $pdo->inTransaction()->willReturn(true);
        $this->shouldThrow(new \RuntimeException('PDO is already in a transaction. Nested transactions are not supported.'))->during('startTransaction');
    }

    public function it_can_commit_a_transaction($pdo)
    {
        $pdo->inTransaction()->willReturn(true);
        $pdo->commit()->shouldBeCalledTimes(1);
        $this->commitTransaction();
    }

    public function it_should_throw_an_exception_if_trying_to_commit_an_unstarted_transaction($pdo)
    {
        $pdo->inTransaction()->willReturn(false);
        $this->shouldThrow(new \RuntimeException('PDO is not in a transaction.'))->during('commitTransaction');
    }

    public function it_can_rollback_a_transaction($pdo)
    {
        $pdo->inTransaction()->willReturn(true);
        $pdo->rollBack()->shouldBeCalledTimes(1);
        $this->rollbackTransaction();
    }
}
