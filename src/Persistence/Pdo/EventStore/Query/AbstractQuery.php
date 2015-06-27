<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

abstract class AbstractQuery implements PdoEventStoreQuery
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $sqlQueryString;

    /**
     * @param \PDO   $pdo
     * @param string $tableName
     * @param string $sql
     */
    public function __construct(\PDO $pdo, $tableName, $sql)
    {
        $this->pdo = $pdo;

        if (!is_string($tableName)) {
            throw new \LogicException('Table name must be a string.');
        }

        if (!is_string($sql)) {
            throw new \LogicException('Sql must be a string.');
        }

        $this->tableName = $tableName;
        $this->sqlQueryString = $sql;
    }

    /**
     * @return \PDOStatement
     */
    public function prepare()
    {
        return $this->pdo->prepare($this->getSql());
    }

    /**
     * @return sql
     */
    protected function getSql()
    {
        return sprintf($this->sqlQueryString, $this->tableName);
    }
}
