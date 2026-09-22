<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();

// Insert permission if not exists
$stmt = $db->prepare("INSERT IGNORE INTO permissions (code, name, module, description) VALUES ('invoices.edit', 'Edit Invoices', 'invoices', 'Allow editing of draft and posted invoices')");
$stmt->execute();

$permId = $db->query("SELECT id FROM permissions WHERE code = 'invoices.edit'")->fetchColumn();

// Grant to Admin (assuming Admin role is ID 1, let's just grant to all roles that have invoices.post)
$roles = $db->query("SELECT role_id FROM role_permissions WHERE permission_id = (SELECT id FROM permissions WHERE code = 'invoices.post')")->fetchAll(PDO::FETCH_COLUMN);

foreach ($roles as $roleId) {
    $db->prepare("INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)")->execute([$roleId, $permId]);
}

echo "Permission invoices.edit created and assigned to roles: " . implode(', ', $roles) . "\n";
