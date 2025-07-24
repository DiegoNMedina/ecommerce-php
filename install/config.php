<?php

namespace Install;

class Config {
    // Database configuration
    const DB_HOST = 'localhost';
    const DB_NAME = 'ecommerce_computers';
    const DB_USER = 'root';
    const DB_PASS = 'root'; // Default MAMP password
    const DB_CHARSET = 'utf8mb4';

    // Application configuration
    const APP_NAME = 'Computer Store';
    const APP_URL = 'http://localhost:8888/ecommerce-php';
    const APP_VERSION = '1.0.0';

    // Security configuration
    const HASH_SALT = 'change_this_to_a_random_string';
    const SESSION_LIFETIME = 3600; // 1 hour

    // Pagination configuration
    const ITEMS_PER_PAGE = 12;

    // Payment configuration
    const ANNUAL_INTEREST_RATE = 10.0; // 10% annual interest

    // Error reporting
    const DEBUG_MODE = true;

    /**
     * Get PDO connection string
     * @return string
     */
    public static function getDsn(): string
    {
        return sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            self::DB_HOST,
            self::DB_NAME,
            self::DB_CHARSET
        );
    }

    /**
     * Get PDO options
     * @return array
     */
    public static function getPdoOptions(): array
    {
        return [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }
}