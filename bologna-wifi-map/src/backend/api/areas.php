<?php
require_once("bootstrap.php");

try {
    $db = new DatabaseHelper();
    $areas = $db->getAllAreas();
    echo json_encode(["success" => true, "results" => $areas]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
