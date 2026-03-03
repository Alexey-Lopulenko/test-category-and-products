<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(array $config): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $dsn  = (string)($config['dsn'] ?? '');
        $user = (string)($config['user'] ?? '');
        $pass = (string)($config['pass'] ?? '');
        $opt  = (array)($config['opt'] ?? []);

        if ($dsn !== '' && stripos($dsn, 'charset=') === false) {
            $dsn .= (str_contains($dsn, '?') ? '&' : ';') . 'charset=utf8mb4';
        }

        $defaults = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $opt = $opt + $defaults + [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ];

        self::$pdo = new PDO($dsn, $user, $pass, $opt);
        return self::$pdo;
    }
}