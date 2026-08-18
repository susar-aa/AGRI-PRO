<?php
$db = new PDO('mysql:host=127.0.0.1;dbname=agri_erp;charset=utf8mb4', 'root', '');
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

$directories = [
    'c:\\xampp\\htdocs\\AGRI PRO\\app\\Models',
    'c:\\xampp\\htdocs\\AGRI PRO\\app\\Controllers',
    'c:\\xampp\\htdocs\\AGRI PRO\\app\\Services',
    'c:\\xampp\\htdocs\\AGRI PRO\\core'
];

$usedTables = [];

foreach ($directories as $dir) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            foreach ($tables as $table) {
                // Check if table is used in a SQL context
                if (
                    preg_match("/FROM\s+[\`]?{$table}[\`]?\b/i", $content) ||
                    preg_match("/INTO\s+[\`]?{$table}[\`]?\b/i", $content) ||
                    preg_match("/UPDATE\s+[\`]?{$table}[\`]?\b/i", $content) ||
                    preg_match("/JOIN\s+[\`]?{$table}[\`]?\b/i", $content)
                ) {
                    $usedTables[$table] = true;
                }
            }
        }
    }
}

$unused = [];
foreach ($tables as $table) {
    if (!isset($usedTables[$table])) {
        $unused[] = $table;
    }
}

echo "Used Tables:\n";
print_r(array_keys($usedTables));
echo "\nUnused Tables:\n";
print_r($unused);
