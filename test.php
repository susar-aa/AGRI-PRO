<?php
require 'core/bootstrap.php';
$search = 'se';
$db = \Core\Database::getInstance();
$query = "SELECT id, name_en, name_si, party_type FROM parties WHERE status = 'active'";
$params = [];
if (!empty($search)) {
    $query .= " AND (name_en LIKE :search OR name_si LIKE :search)";
    $params['search'] = '%' . $search . '%';
}
$query .= " ORDER BY name_en ASC LIMIT 50";
$stmt = $db->prepare($query);
$stmt->execute($params);
$parties = $stmt->fetchAll();
var_dump($parties);
