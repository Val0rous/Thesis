<?php
require_once("../utils/DatabaseHelper.php");
$db = new DatabaseHelper();

$test = $db->getCrowdingAttendance("2024-01-01");
echo "Ok";