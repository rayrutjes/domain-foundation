<?php

namespace RayRutjes\DomainFoundation\Stub\Persistence\Pdo\EventStore\Query;

use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query\AbstractQuery;

class AbstractQueryStub extends AbstractQuery
{
    public function __construct(\PDO $pdo, $tableName, $sql)
    {
        parent::__construct($pdo, $tableName, $sql);
    }
}
