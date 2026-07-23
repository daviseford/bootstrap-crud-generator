<?php

declare(strict_types=1);

/**
 * Create a UTF-8 PDO connection for the generated CRUD application.
 */
function db(): PDO
{
    static $connection = null;

    if ($connection instanceof PDO) {
        return $connection;
    }

    $host = '';       // Database host, for example: localhost
    $database = '';   // Database name
    $username = '';   // Database user
    $password = '';   // Database password

    $dsn = "mysql:host={$host};dbname={$database};charset=utf8mb4";
    $connection = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $connection;
}

/**
 * Escape a value before placing it in HTML.
 *
 * @param mixed $value
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
