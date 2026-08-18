<?php
require 'core/bootstrap.php';
$db = \Core\Database::getInstance();
$hash = password_hash('password123', PASSWORD_DEFAULT);
$db->query("UPDATE users SET password_hash = '{$hash}' WHERE username = 'admin'");
echo "Admin password reset to 'password123'.";
