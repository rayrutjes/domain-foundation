<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

final class SelectQuery extends AbstractQuery
{
    protected $sql = <<<MYSQL
SELECT * FROM `%s`
WHERE `aggregate_id` = :aggregate_id
AND `aggregate_type` = :aggregate_type
ORDER BY `aggregate_version`;
MYSQL;

    /**
     * @param \PDO   $pdo
     * @param string $tableName
     */
    public function __construct(\PDO $pdo, $tableName)
    {
        parent::__construct($pdo, $tableName, $this->sql);
    }
}
