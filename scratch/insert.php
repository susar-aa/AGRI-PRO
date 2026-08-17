<?php
require_once __DIR__ . '/../core/bootstrap.php';
$db = \Core\Database::getInstance();
$accId = 1;
$categories = ['Labour charges', 'Fertilizers & Chemicals', 'Seeds & Plants', 'Machinery & Equipment', 'Fuel & Transport', 'Irrigation & Water', 'Maintenance & Repairs', 'Miscellaneous'];
foreach ($categories as $cat) {
    $db->query("INSERT IGNORE INTO expense_categories (name, linked_account_id, is_active) VALUES ('$cat', $accId, 1)");
}
echo "Inserted categories.";
