<?php

return [
    'db' => [
        'dsn'  => 'mysql:host=db;dbname=catalog_test;charset=utf8mb4',
        'user' => 'app',
        'pass' => 'app',
        'opt'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ],
];