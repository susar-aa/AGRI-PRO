<?php
/**
 * Database Setup Script for Agri Co-Op ERP
 * Runs schema and seeders on local XAMPP MySQL environment.
 */

$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';

echo "=== AGRI CO-OP ERP DATABASE SETUP ===\n";

try {
    // 1. Connect to MySQL Server (without selecting DB)
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "[SUCCESS] Connected to MySQL Server.\n";

    // 2. Read and execute schema.sql
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("schema.sql not found at {$schemaFile}");
    }
    $schemaSql = file_get_contents($schemaFile);
    $pdo->exec($schemaSql);
    echo "[SUCCESS] Executed schema.sql successfully.\n";

    // 3. Read and execute seeders.sql
    $seedersFile = __DIR__ . '/seeders.sql';
    if (!file_exists($seedersFile)) {
        throw new Exception("seeders.sql not found at {$seedersFile}");
    }
    $seedersSql = file_get_contents($seedersFile);
    $pdo->exec($seedersSql);
    echo "[SUCCESS] Executed seeders.sql successfully.\n";

    echo "=== DATABASE SETUP COMPLETED SUCCESSFULLY! ===\n";

} catch (Exception $e) {
    echo "[ERROR] Database Setup Failed: " . $e->getMessage() . "\n";
    exit(1);
}
