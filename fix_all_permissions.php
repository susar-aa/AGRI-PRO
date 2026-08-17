<?php
$host = '127.0.0.1';
$port = 3306;
$user = 'root';
$pass = '';
$dbName = 'agri_erp';

echo "=== AGRI CO-OP ERP PERMISSIONS AUTO-FIX ===\n";

try {
    $dsn = "mysql:host={$host};dbname={$dbName};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    // 1. Scan all controllers for Auth::requirePermission
    $controllersDir = __DIR__ . '/app/Controllers';
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllersDir));
    
    $requiredPermissions = [];
    
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            preg_match_all("/Auth::requirePermission\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $perm) {
                    $requiredPermissions[$perm] = true;
                }
            }
        }
    }
    
    $requiredPermissions = array_keys($requiredPermissions);
    echo "Found " . count($requiredPermissions) . " unique permissions required by controllers.\n";
    
    // 2. Fetch existing permissions
    $stmt = $pdo->query("SELECT code FROM permissions");
    $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $missing = array_diff($requiredPermissions, $existing);
    
    if (empty($missing)) {
        echo "No missing permissions found.\n";
    } else {
        echo "Found " . count($missing) . " missing permissions. Inserting...\n";
        
        $insertPerm = $pdo->prepare("INSERT INTO permissions (code, name, module, description) VALUES (:code, :name, :module, :description)");
        
        foreach ($missing as $code) {
            $parts = explode('.', $code);
            $module = $parts[0] ?? 'general';
            $action = $parts[1] ?? 'access';
            
            $name = ucfirst($action) . ' ' . ucfirst(str_replace('_', ' ', $module));
            $desc = "Auto-generated permission for {$code}";
            
            $insertPerm->execute([
                'code' => $code,
                'name' => $name,
                'module' => $module,
                'description' => $desc
            ]);
        }
        
        // 3. Assign all permissions to Super Admin (1) and Admin (2)
        echo "Assigning all permissions to Super Admin and Admin...\n";
        
        $pdo->exec("
            INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT 1, id FROM permissions
        ");
        
        $pdo->exec("
            INSERT IGNORE INTO role_permissions (role_id, permission_id)
            SELECT 2, id FROM permissions
        ");
        
        echo "[SUCCESS] All missing permissions have been added and assigned to Admins!\n";
    }
    
} catch (Exception $e) {
    echo "[ERROR] Permissions Fix Failed: " . $e->getMessage() . "\n";
    exit(1);
}
