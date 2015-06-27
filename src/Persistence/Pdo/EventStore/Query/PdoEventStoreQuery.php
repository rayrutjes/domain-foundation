<?php

namespace RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\Query;

interface PdoEventStoreQuery
{
    /**
     * @return \PDOStatement
     */
    public function prepare();
}
