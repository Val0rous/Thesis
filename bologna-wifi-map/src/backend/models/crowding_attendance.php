<?php
header('Content-Type: application/json');
require_once "../controllers/DatabaseHelper.php";
// First data from April 1, 2021
$crowdingApiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affollamento/records?select=codice_zona%2C%20data%2C%20giorno%2C%20ora%2C%20affollamento_medio&order_by=codice_zona%2C%20data%20asc&limit=100&refine=data%3A";
$crowdingTotalCountUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affollamento/records?order_by=data%20asc&limit=0";
$attendanceApiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affluenza/records?select=codice_zona%2C%20data%2C%20giorno%2C%20ora%2C%20affluenza_media&order_by=codice_zona%2C%20data%20asc&limit=100&refine=data%3A";
$attendanceTotalCountUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affluenza/records?order_by=data%20asc&limit=0";
$crowdingStartDate = new DateTime("2024-01-15");    // Affollamento
$attendanceStartDate = new DateTime("2024-01-01");  // Affluenza
//$attendanceStartDate = new DateTime("2025-01-01");    // debug
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

    Found out you get a -1 on spring forward day, then another -1 on fall back day,
    which gets evened out by 2 consecutive 25-entry days right after fall back day.
    This means we have a 1 hour offset for the entirety of summer,
    so if offset is negative we need to consider the next day and perform yet
    another query, or store the entire 23 values in an array which will be then
    completed with the missing value and saved to db, and the cycle repeats;
    if offset is positive (which shouldn't be happening here), then just store
    the extra value in another variable.
*/


// Fetch Crowding and Attendance data day by day in 100 item chunks, then add them to db
for ($date = $attendanceStartDate; $date <= $endDate; $date->modify("+1 day")) {
    $formattedDate = $date->format("Y-m-d");
    echo "\r" . $formattedDate;
    $crowdingBaseUrl = $crowdingApiUrl . $formattedDate;
    $attendanceBaseUrl = $attendanceApiUrl . $formattedDate;
    $crowdingOffset = 0;
    $attendanceOffset = 0;
    $crowdingTotalDailyCount = 0;
    $attendanceTotalDailyCount = 0;
    $crowdingIsIncreasedFetchedCount = false;
    $attendanceIsIncreasedFetchedCount = false;
    $crowding = [];
    $attendance = [];

    $isCrowding = ($date >= $crowdingStartDate);
    $crowdingHourOffset = 0;
    $attendanceHourOffset = 0;

    if ($isCrowding) {
        // Crowding
        do {
            $crowdingUrl = $crowdingBaseUrl . "&offset=" . $crowdingOffset;
            $crowdingResponse = file_get_contents($crowdingUrl);

            if ($crowdingResponse !== false) {
                $crowdingData = json_decode($crowdingResponse, true);
                if (is_array($crowdingData)) {
                    if (!$crowdingIsIncreasedFetchedCount) {
                        $crowdingTotalDailyCount = $crowdingData["total_count"];
                        $crowdingFetchedCount += $crowdingTotalDailyCount;
                    }
                    foreach ($crowdingData["results"] as $item) {
                        $crowding[] = $item;
                    }
                    $crowdingOffset += 100;
                    $crowdingIsIncreasedFetchedCount = true;
                } else {
                    error_log("Invalid crowding JSON response for date $formattedDate: $crowdingResponse");
                }
            } else {
                error_log("Failed to fetch crowding data for date $formattedDate");
            }
        } while ($crowdingOffset < $crowdingTotalDailyCount);
    }

    // Attendance
    do {
        $attendanceUrl = $attendanceBaseUrl . "&offset=" . $attendanceOffset;
        $attendanceResponse = file_get_contents($attendanceUrl);

        if ($attendanceResponse !== false) {
            $attendanceData = json_decode($attendanceResponse, true);
            if (is_array($attendanceData)) {
                if (!$attendanceIsIncreasedFetchedCount) {
                    $attendanceTotalDailyCount = $attendanceData["total_count"];
                    $attendanceFetchedCount += $attendanceTotalDailyCount;
                }
                foreach ($attendanceData["results"] as $item) {
                    $attendance[] = $item;
                }
                $attendanceOffset += 100;
                $attendanceIsIncreasedFetchedCount = true;
            } else {
                error_log("Invalid attendance JSON response for date $formattedDate: $attendanceResponse");
            }
        } else {
            error_log("Failed to fetch attendance data for date $formattedDate");
        }
    } while ($attendanceOffset < $attendanceTotalDailyCount);

    if ((count($crowding) > 0 || !$isCrowding)
        && count($attendance) > 0) {

    }
}
echo PHP_EOL;
echo "Fetched crowding list count: " . $crowdingFetchedCount . PHP_EOL;
echo "Fetched attendance list count: " . $attendanceFetchedCount . PHP_EOL;