<?php
require_once("bootstrap.php");

if (isset($_POST["date"])) {
    try {
        $db = new DatabaseHelper();
        $movements = $db->getMovements($_POST["date"]);
        echo json_encode(["success" => true, "results" => $movements]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Date parameter is required."]);
}
