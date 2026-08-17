-- SQL to fix plural expenses permissions (ensuring compatibility with controller requirePermission checks)

INSERT IGNORE INTO permissions (code, name, module, description) VALUES 
('expenses.view', 'View Expenses', 'finance', 'View and search expense vouchers'),
('expenses.create', 'Create Expenses', 'finance', 'Create new draft expenses'),
('expenses.edit', 'Edit Expenses', 'finance', 'Edit draft expense vouchers'),
('expenses.submit', 'Submit Expenses', 'finance', 'Submit draft expenses for approval'),
('expenses.approve', 'Approve Expenses', 'finance', 'Approve pending expense vouchers'),
('expenses.post', 'Post Expenses', 'finance', 'Post approved expenses to ledger'),
('expenses.reverse', 'Reverse Expenses', 'finance', 'Create reversal vouchers for posted expenses'),
('expenses.cancel', 'Cancel Expenses', 'finance', 'Cancel draft or pending expenses');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
CROSS JOIN permissions p
WHERE p.code IN (
    'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.submit',
    'expenses.approve', 'expenses.post', 'expenses.reverse', 'expenses.cancel'
);
