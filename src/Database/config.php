<?php

const DB_CONFIG = [
    'host'     => 'localhost',
    'dbname'   => 'music_showcase_db',
    'user'     => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];

/**
 * Utility function to get a PDO connection instance.
 * @return \PDO
 */
function get_db_connection(): \PDO {
    $dsn = "mysql:host=" . DB_CONFIG['host'] . ";dbname=" . DB_CONFIG['dbname'] . ";charset=" . DB_CONFIG['charset'];
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new \PDO( $dsn, DB_CONFIG['user'], DB_CONFIG['password'], $options );
    } catch ( \PDOException $e ) {
        throw new \PDOException( $e->getMessage(), (int) $e->getCode() );
    }
}