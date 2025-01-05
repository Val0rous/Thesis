<?php
// Database configuration
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bologna_wifi_map";

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Set error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Define the query
    $query = "SELECT * FROM areas";

    // Prepare and execute the query
    $stmt = $db->query($query);

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the results
    foreach ($results as $row) {
        print_r($row);
        echo PHP_EOL; // Add a newline for better readability
    }

} catch (PDOException $e) {
    // Handle connection or query errors
    echo "Error: " . $e->getMessage();
    exit;
}