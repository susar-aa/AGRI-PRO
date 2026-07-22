<?php
namespace Core;

use PDO;

class Auth {
    private static ?array $currentUser = null;
    private static ?array $userPermissions = null;

    public static function check(): bool {
        return Session::has('user_id');
    }

    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        if (self::$currentUser === null) {
            $db = Database::getInstance();
            $stmt = $db->prepare("SELECT id, username, email, full_name, phone, status FROM users WHERE id = :id AND status = 'active' LIMIT 1");
            $stmt->execute(['id' => Session::get('user_id')]);
            $user = $stmt->fetch();
            if ($user) {
                self::$currentUser = $user;
            } else {
                self::logout();
                return null;
            }
        }
        return self::$currentUser;
    }

    public static function id(): ?int {
        return Session::get('user_id');
    }

    public static function attempt(string $username, string $password): bool {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, password_hash, full_name, status FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && $user['status'] === 'active' && password_verify($password, $user['password_hash'])) {
            Session::set('user_id', (int)$user['id']);
            Session::set('username', $user['username']);
            Session::set('full_name', $user['full_name']);
            
            // Update last_login
            $upd = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $upd->execute(['id' => $user['id']]);

            // Audit log
            \App\Services\AuditService::log('login', 'auth', $user['id'], null, ['username' => $username]);

            return true;
        }
        return false;
    }

    public static function logout(): void {
        if (self::check()) {
            \App\Services\AuditService::log('logout', 'auth', self::id(), null, null);
        }
        Session::destroy();
        self::$currentUser = null;
        self::$userPermissions = null;
    }

    public static function permissions(): array {
        if (self::$userPermissions !== null) {
            return self::$userPermissions;
        }

        $userId = self::id();
        if (!$userId) {
            return [];
        }

        $db = Database::getInstance();
        // Get permissions for user roles
        $stmt = $db->prepare("
            SELECT DISTINCT p.code 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            JOIN user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        self::$userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return self::$userPermissions;
    }

    public static function hasPermission(string $permissionCode): bool {
        $perms = self::permissions();
        return in_array($permissionCode, $perms, true);
    }

    public static function requirePermission(string $permissionCode): void {
        if (!self::check()) {
            Helper::redirect('login');
            exit;
        }

        if (!self::hasPermission($permissionCode)) {
            http_response_code(403);
            echo "<h1>403 Forbidden</h1><p>You do not have permission to access this module ({$permissionCode}).</p>";
            exit;
        }
    }
}
