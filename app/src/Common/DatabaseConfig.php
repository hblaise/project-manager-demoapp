<?php

namespace App\Common;

use InvalidArgumentException;

class DatabaseConfig
{
    private static string $host;
    private static int $port;
    private static string $username;
    private static string $password;
    private static string $database;

    public static function set(array $config): void
    {
        self::$host = $config['host'] ?? throw new InvalidArgumentException('Host is missing from database config!');
        self::$port = $config['port'] ?? throw new InvalidArgumentException('Port is missing from database config!');
        self::$username = $config['username'] ?? throw new InvalidArgumentException('Username is missing from database config!');
        self::$password = $config['password'] ?? throw new InvalidArgumentException('Password is missing from database config!');
        self::$database = $config['database'] ?? throw new InvalidArgumentException('Database is missing from database config!');
    }

    public static function getHost(): string
    {
        return self::$host;
    }

    public static function getPort(): int
    {
        return self::$port;
    }

    public static function getUsername(): string
    {
        return self::$username;
    }

    public static function getPassword(): string
    {
        return self::$password;
    }

    public static function getDatabase(): string
    {
        return self::$database;
    }

    public static function getDsn(): string
    {
        return "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$database . ";charset=utf8mb4";
    }
}