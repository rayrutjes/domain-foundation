<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Persistence\Pdo\UnitOfWork;

use RayRutjes\DomainFoundation\Persistence\Pdo\UnitOfWork\PdoTransactionManager;

class PdoTransactionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PdoTransactionManager
     */
    private $transactionManager;

    private $pdo;

    public function setUp()
    {
        $this->pdo = $this->getMockBuilder('PDO')
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->transactionManager = new PdoTransactionManager($this->pdo);
    }

    public function testTransactionCanBeStarted()
    {
        $this->pdo->method('inTransaction')->willReturn(false);

        $this->pdo->expects($this->once())
                  ->method('beginTransaction');

        $this->transactionManager->startTransaction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTransactionCannotBeStartedIfAlreadyStarted()
    {
        $this->pdo->method('inTransaction')->willReturn(true);

        $this->transactionManager->startTransaction();
    }

    public function testTransactionCanCommitted()
    {
        $this->pdo->method('inTransaction')->willReturn(true);

        $this->pdo->expects($this->once())
                  ->method('commit');

        $this->transactionManager->commitTransaction();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testTransactionCannotBeCommittedIfItHasNotBeenStarted()
    {
        $this->pdo->method('inTransaction')->willReturn(false);

        $this->transactionManager->commitTransaction();
    }

    public function testTransactionCanBeRolledBack()
    {
        $this->pdo->method('inTransaction')->willReturn(true);

        $this->pdo->expects($this->once())
                  ->method('rollBack');

        $this->transactionManager->rollbackTransaction();
    }
}
