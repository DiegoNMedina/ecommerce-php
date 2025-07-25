<?php

namespace Core;

use Install\Config;

class Database
{
    private static ?\PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): \PDO
    {
        if (self::$instance === null) {
            try {
                self::$instance = new \PDO(
                    Config::getDsn(),
                    Config::DB_USER,
                    Config::DB_PASS,
                    Config::getPdoOptions()
                );
            } catch (\PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$instance;
    }

    public static function beginTransaction(): void
    {
        self::getInstance()->beginTransaction();
    }

    public static function commit(): void
    {
        self::getInstance()->commit();
    }

    public static function rollBack(): void
    {
        self::getInstance()->rollBack();
    }
}
