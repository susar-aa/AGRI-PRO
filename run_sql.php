<?php
require 'core/bootstrap.php';
$db = Core\Database::getInstance();
$sql = file_get_contents('C:\\Users\\susar.aa\\.gemini\\antigravity-ide\\brain\\48b497cd-7ed2-4e73-ac8a-79cffac77133\\allow_null_fields.sql');
$db->exec($sql);
echo 'Success';
