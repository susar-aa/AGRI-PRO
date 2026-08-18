<?php
/**
 * Database Configuration
 */

$isProduction = false;
if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'agripro.suzxlabs.com') !== false) {
    $isProduction = true;
} elseif (PHP_SAPI === 'cli' && stripos(PHP_OS, 'WIN') === false) {
    // If running in CLI on a Linux server (Plesk), assume production
    $isProduction = true;
}

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => getenv('DB_PORT') ?: '3306',
    'database' => getenv('DB_NAME') ?: 'agri_erp',
    'username' => getenv('DB_USER') ?: ($isProduction ? 'suzxlabs' : 'root'),
    'password' => getenv('DB_PASS') ?: ($isProduction ? 'Susara@200611003614' : ''),
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];
