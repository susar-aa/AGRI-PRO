<?php
namespace App\Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use Core\Session;
use Core\Helper;
use Exception;

class UserController extends Controller {

    public function index(): void {
        Auth::requirePermission('users.manage');

        $db = Database::getInstance();
        $users = $db->query("SELECT id, username, full_name, email, phone, status, last_login, created_at FROM users WHERE deleted_at IS NULL ORDER BY id ASC")->fetchAll();

        $this->render('users/index', [
            'pageTitle' => 'Users Management',
            'activeNav' => 'users',
            'users' => $users
        ]);
    }

    public function create(): void {
        Auth::requirePermission('users.manage');

        $this->render('users/form', [
            'pageTitle' => 'Create New User',
            'activeNav' => 'users',
            'user' => null
        ]);
    }

    public function store(): void {
        Auth::requirePermission('users.manage');
        $this->validateCsrf();

        try {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $status = $_POST['status'] ?? 'active';

            if (empty($username) || empty($fullName) || empty($password)) {
                throw new Exception("Username, Full Name, and Password are required.");
            }

            $db = Database::getInstance();

            // Check if username exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetch()) {
                throw new Exception("Username already exists.");
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $insertStmt = $db->prepare("
                INSERT INTO users (username, full_name, email, phone, password_hash, status, created_at, updated_at) 
                VALUES (:username, :full_name, :email, :phone, :password_hash, :status, NOW(), NOW())
            ");
            $insertStmt->execute([
                'username' => $username,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => $passwordHash,
                'status' => $status
            ]);

            Session::setFlash('success', 'User created successfully.');
            Helper::redirect('modules/users');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Helper::redirect('modules/users/create');
        }
    }

    public function edit(): void {
        Auth::requirePermission('users.manage');

        $id = (int)($_GET['id'] ?? 0);
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, full_name, email, phone, status FROM users WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            Session::setFlash('error', 'User not found.');
            Helper::redirect('modules/users');
        }

        $this->render('users/form', [
            'pageTitle' => 'Edit User',
            'activeNav' => 'users',
            'user' => $user
        ]);
    }

    public function update(): void {
        Auth::requirePermission('users.manage');
        $this->validateCsrf();

        try {
            $id = (int)$_POST['id'];
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $status = $_POST['status'] ?? 'active';

            if (empty($username) || empty($fullName)) {
                throw new Exception("Username and Full Name are required.");
            }

            $db = Database::getInstance();

            // Check if username exists for OTHER users
            $stmt = $db->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->execute(['username' => $username, 'id' => $id]);
            if ($stmt->fetch()) {
                throw new Exception("Username already exists.");
            }

            if (!empty($password)) {
                // Update with password
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $updateStmt = $db->prepare("
                    UPDATE users SET 
                        username = :username, full_name = :full_name, email = :email, 
                        phone = :phone, status = :status, password_hash = :password_hash, updated_at = NOW()
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    'username' => $username, 'full_name' => $fullName, 'email' => $email, 
                    'phone' => $phone, 'status' => $status, 'password_hash' => $passwordHash, 'id' => $id
                ]);
            } else {
                // Update without password
                $updateStmt = $db->prepare("
                    UPDATE users SET 
                        username = :username, full_name = :full_name, email = :email, 
                        phone = :phone, status = :status, updated_at = NOW()
                    WHERE id = :id
                ");
                $updateStmt->execute([
                    'username' => $username, 'full_name' => $fullName, 'email' => $email, 
                    'phone' => $phone, 'status' => $status, 'id' => $id
                ]);
            }

            Session::setFlash('success', 'User updated successfully.');
            Helper::redirect('modules/users');
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
            Helper::redirect('modules/users/edit?id=' . ($_POST['id'] ?? 0));
        }
    }

    public function toggleStatus(): void {
        Auth::requirePermission('users.manage');
        $this->validateCsrf();

        try {
            $id = (int)$_POST['id'];
            $db = Database::getInstance();
            
            $stmt = $db->prepare("SELECT status FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception("User not found.");
            }

            $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';

            $update = $db->prepare("UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id");
            $update->execute(['status' => $newStatus, 'id' => $id]);

            Session::setFlash('success', "User status changed to {$newStatus}.");
        } catch (Exception $e) {
            Session::setFlash('error', $e->getMessage());
        }

        Helper::redirect('modules/users');
    }
}
