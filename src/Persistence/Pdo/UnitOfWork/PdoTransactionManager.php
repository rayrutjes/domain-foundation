<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\UnitOfWork;

use RayRutjes\DomainFoundation\UnitOfWork\TransactionManager;

class PdoTransactionManager implements TransactionManager
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function startTransaction()
    {
        if ($this->pdo->inTransaction()) {
            throw new \RuntimeException('PDO is already in a transaction. Nested transactions are not supported.');
        }
        $this->pdo->beginTransaction();
    }

    public function commitTransaction()
    {
        if (!$this->pdo->inTransaction()) {
            throw new \RuntimeException('PDO is not in a transaction.');
        }
        $this->pdo->commit();
    }

    public function rollbackTransaction()
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }
}
