<?php
require_once("bootstrap.php");

if (isset($_POST["date"])) {
    try {
        $db = new DatabaseHelper();
        $crowdingAttendance = $db->getCrowdingAttendance($_POST["date"]);
        echo json_encode(["success" => true, "results" => $crowdingAttendance]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Date parameter is required."]);
}
