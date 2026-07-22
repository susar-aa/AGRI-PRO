<?php
namespace App\Services;

use Core\Database;
use Core\Auth;

class AuditService {
    public static function log(string $action, string $module, ?int $recordId = null, ?array $oldValues = null, ?array $newValues = null): void {
        try {
            $db = Database::getInstance();
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, module, record_id, old_values, new_values, ip_address, user_agent)
                VALUES (:user_id, :action, :module, :record_id, :old_values, :new_values, :ip_address, :user_agent)
            ");
            $stmt->execute([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => $module,
                'record_id' => $recordId,
                'old_values' => $oldValues ? json_encode($oldValues, JSON_UNESCAPED_UNICODE) : null,
                'new_values' => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE) : null,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'CLI'
            ]);
        } catch (\Exception $e) {
            // Fail silently on audit logging to avoid breaking primary transactions
            error_log("AuditLog error: " . $e->getMessage());
        }
    }
}
