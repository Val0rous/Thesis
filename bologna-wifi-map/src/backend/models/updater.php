<?php
require_once "../controllers/DatabaseHelper.php";

$db = new DatabaseHelper();
$areas = $db->getAllAreas();
foreach ($areas as $area) {
    echo $area["zone_id"] . PHP_EOL;
}