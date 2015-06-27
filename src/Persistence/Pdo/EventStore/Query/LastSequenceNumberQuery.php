<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

final class LastSequenceNumberQuery extends AbstractQuery
{
    //    protected $sql= <<<MYSQL
//SELECT COALESCE(MAX(`aggregate_version`), 0) FROM `%s`
//WHERE `aggregate_id` = :aggregate_id
//AND `aggregate_type` = :aggregate_type;
//MYSQL;
//
//    /**
//     * @param \PDO   $pdo
//     * @param string $tableName
//     */
//    public function __construct(\PDO $pdo, $tableName)
//    {
//        parent::__construct($pdo, $tableName, $this->sql);
//    }
}
