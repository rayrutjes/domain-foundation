<?php

namespace RayRutjes\DomainFoundation\Test\Resources;

use RayRutjes\DomainFoundation\Persistence\Pdo\EventStore\PdoEventStore;

abstract class DatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{
    // only instantiate pdo once for test clean-up/fixture load
    protected static $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    final public function getConnection()
    {

        if ($this->conn === null) {
            if (getenv('TRAVIS') === 'true') {
                $dsn = getenv('PDO_MYSQL_TEST_DSN');
                $user = getenv('PDO_MYSQL_TEST_USER');
                $pass = '';
                $dbName = getenv('PDO_MYSQL_TEST_DB_NAME');
            } else {
                $dsn = $GLOBALS['PDO_MYSQL_TEST_DSN'];
                $user = $GLOBALS['PDO_MYSQL_TEST_USER'];
                $pass = $GLOBALS['PDO_MYSQL_TEST_PASS'];
                $dbName = $GLOBALS['PDO_MYSQL_TEST_DB_NAME'];
            }

            if (self::$pdo == null) {
                self::$pdo = new \PDO($dsn, $user, $pass);
            }

            $this->createTables();

            $this->conn = $this->createDefaultDBConnection(self::$pdo, $dbName);
        }

        return $this->conn;
    }

    private function createTables()
    {
        $eventStore = new PdoEventStore(self::$pdo);
        $eventStore->createTable();
    }
}
