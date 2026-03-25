<?php

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        require __DIR__ . '/../../config/database.php';

        if (!isset($pdo) || !$pdo instanceof PDO) {
            throw new RuntimeException('PDO connection is not available from config/database.php');
        }

        self::$connection = $pdo;
        return self::$connection;
    }
}
