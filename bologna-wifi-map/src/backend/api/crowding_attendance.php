<?php
require_once("bootstrap.php");

// Initialize variables
$date = null;
$rawPostData = file_get_contents("php://input");

// Check for JSON input
if (!empty($rawPostData)) {
    $decodedData = json_decode($rawPostData, true);
    if (isset($decodedData["date"])) {
        $date = $decodedData["date"];
    }
}

// Check for form-encoded input (optional fallback)
if (isset($_POST["date"])) {
    $date = $_POST["date"];
}

if (isset($date)) {
    try {
        $db = new DatabaseHelper();
        $crowdingAttendance = $db->getCrowdingAttendance($date);
        echo json_encode(["success" => true, "results" => $crowdingAttendance]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Date parameter is required."]);
}
