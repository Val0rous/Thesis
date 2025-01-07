<?php
header('Content-Type: application/json');
require_once "../controllers/DatabaseHelper.php";
// First data from April 1, 2021
$crowdingApiUrl = "";
$crowdingTotalCountUrl = "";
$attendanceApiUrl = "";
$attendanceTotalCountUrl = "";
$crowdingStartDate = new DateTime("2024-01-15");    // Affollamento
$attendanceStartDate = new DateTime("2024-01-01");  // Affluenza
//$startDate = new DateTime("2024-12-01");    // debug
$endDate = new DateTime();
$crowdingTotalCount = 0;
$crowdingFetchedCount = 0;
$crowdingCounter = 0;
$attendanceTotalCount = 0;
$attendanceFetchedCount = 0;
$attendanceCounter = 0;
$db = new DatabaseHelper();

$crowdingResponse = file_get_contents($crowdingTotalCountUrl);
if ($crowdingResponse !== false) {
    $crowdingTotalCount = json_decode($crowdingResponse, true)["total_count"];
} else {
    error_log("Failed to fetch data for crowding total count");
}
echo "Total count of crowding: " . $crowdingTotalCount . PHP_EOL;

$attendanceResponse = file_get_contents($attendanceTotalCountUrl);
if ($attendanceResponse !== false) {
    $attendanceTotalCount = json_decode($attendanceResponse, true)["total_count"];
} else {
    error_log("Failed to fetch data for attendance total count");
}
echo "Total count of attendance: " . $attendanceTotalCount . PHP_EOL;

/*  TODO: both start with T00:00:00+00:00, meaning from midnight GMT.
    Save all data using GMT as time zone. Just grab the first 2 numbers after T

    Day before spring forward: March 30, 2024 -> 24 items
    Day of spring forward: March 31, 2024 -> 24 or 25 items
    Day after spring forward: April 1, 2024 -> 24 items

    Day before fall back: October 26, 2024 -> 24 items
    Day of fall back: October 27, 2024 -> 23 items
    Day after fall back: October 28, 2024 -> 25 items
    2 days later: October 29, 2024 -> 25 items
    3 days later: October 30, 2024 -> 24 items

    Gotta save an offset for both crowding and attendance,
    in two different arrays both indexed by association of zoneId
    (aeroporto, archiginnasio, ...), as you'll need it through the entire
    DST duration

    hourOffset = totalCountForEachZoneId - 24
    a total count of 23 yields -1
    a total count of 25 yields +1

    Print offset on each iteration if offset is not zero, along with the date
*/


// Fetch Crowding data day by day in 100 item chunks, then add them to db
for ($date = $crowdingStartDate; $date <= $endDate; $date->modify("+1 day")) {
    $formattedDate = $date->format("Y-m-d");
    echo "\r" . $formattedDate;
    $baseUrl = $crowdingApiUrl . $formattedDate;
}

// Fetch Attendance data day by day in 100 item chunks, then add them to db
for ($date = $attendanceStartDate; $date <= $endDate; $date->modify("+1 day")) {
    $formattedDate = $date->format("Y-m-d");
    echo "\r" . $formattedDate;
    $baseUrl = $attendanceApiUrl . $formattedDate;
}