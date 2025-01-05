<?php
require_once "../controllers/DatabaseHelper.php";
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bologna_wifi_map";

try {
    $db = new DatabaseHelper();
    $query = "SELECT * FROM areas";
    $results = $db->query($query);

    if ($results !== null) {
        // Display the results
        foreach ($results as $row) {
            print_r($row);
            echo PHP_EOL; // Add a newline for better readability
        }
    }

    $query = "SELECT * FROM coordinates";
    $results = $db->query($query);

    if ($results !== null) {
        // Display the results
        foreach ($results as $row) {
            print_r($row);
            echo PHP_EOL; // Add a newline for better readability
        }
    }

} catch (PDOException $e) {
    // Handle connection or query errors
    echo "Error: " . $e->getMessage();
    exit;
}