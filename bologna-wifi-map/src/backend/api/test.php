<?php
require_once("../utils/DatabaseHelper.php");
$db = new DatabaseHelper();

$areas = $db->getAllAreas();
echo $areas;