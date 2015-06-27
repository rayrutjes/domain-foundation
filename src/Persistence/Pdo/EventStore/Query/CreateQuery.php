<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

final class CreateQuery extends AbstractQuery
{
    private $sql = <<<MYSQL
CREATE TABLE `%s` (
    `checkpoint` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `aggregate_id` VARCHAR(100) NOT NULL,
    `aggregate_type` VARCHAR(100) NOT NULL,
    `aggregate_version` INT(11) NOT NULL,
    `event_id` VARCHAR(100) NOT NULL,
    `event_payload` TEXT NOT NULL,
    `event_payload_type` VARCHAR(100) NOT NULL,
    `event_metadata` TEXT NOT NULL,
    `event_metadata_type` VARCHAR(100) NOT NULL,
    `commit_id` VARCHAR(100),
    `committed_at` DATETIME,
    PRIMARY KEY (`checkpoint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
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
