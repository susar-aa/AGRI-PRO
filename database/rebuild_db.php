<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$dbName = 'agri_erp';

echo "=== AGRI CO-OP ERP DATABASE REBUILD ===\n";

try {
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
    $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$dbName}`");
    
    $files = [
        'schema.sql',
        'missing_tables.sql',
        'seeders.sql',
        'plantation_migration.sql',
        'expenses_permissions_fix.sql',
        'add_posting_date.sql',
        'add_journal_posting_fields.sql',
        'plantation_harvests_migration.sql',
        'plantation_transfers_migration.sql',
        'brick_manufacturing_migration.sql',
        'cash_bank_cheque_migration.sql',
        'member_registration_migration.sql',
        'fd_system_migration.sql'
    ];
    
    foreach ($files as $file) {
        $filePath = __DIR__ . '/' . $file;
        if (!file_exists($filePath)) {
            echo "[WARNING] File not found: {$file}\n";
            continue;
        }
        $sql = file_get_contents($filePath);
        $pdo->exec($sql);
        echo "[SUCCESS] Executed {$file} successfully.\n";
    }
    
    echo "=== DATABASE REBUILD COMPLETED SUCCESSFULLY! ===\n";
} catch (Exception $e) {
    echo "[ERROR] Database Setup Failed: " . $e->getMessage() . "\n";
    exit(1);
}
