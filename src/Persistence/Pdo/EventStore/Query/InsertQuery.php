<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

final class InsertQuery extends AbstractQuery
{
    protected $sql = <<<MYSQL
INSERT INTO `%s` (
    `aggregate_id`,
    `aggregate_type`,
    `aggregate_version`,
    `event_id`,
    `event_payload`,
    `event_payload_type`,
    `event_metadata`,
    `event_metadata_type`,
    `commit_id`,
    `committed_at`
) VALUES (
    :aggregate_id,
    :aggregate_type,
    :aggregate_version,
    :event_id,
    :event_payload,
    :event_payload_type,
    :event_metadata,
    :event_metadata_type,
    :commit_id,
    :committed_at
);
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
