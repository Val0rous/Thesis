<?php
header('Content-Type: application/json');
require_once "../controllers/DatabaseHelper.php";
// First data from April 1, 2021
$apiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?select=data_evento%2C%20hour%2C%20giorno%2C%20percentile_50%2C%20tot_pass%2C%20area_from%2C%20area_to&order_by=area_from%2C%20area_to%2C%20hour%20asc&limit=100&refine=data_evento%3A";
$totalCountUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?order_by=data_evento%20asc&limit=0";
$startDate = new DateTime("2021-04-01");
//$startDate = new DateTime("2024-12-01");    // debug
$endDate = new DateTime();
$totalCount = 0;
$fetchedCount = 0;
$counter = 0;
$db = new DatabaseHelper();

$response = file_get_contents($totalCountUrl);
if ($response !== false) {
    $totalCount = json_decode($response, true)["total_count"];
} else {
    error_log("Failed to fetch data for total count");
}
echo "Total count of movements: " . $totalCount . PHP_EOL;

// Fetch data day by day in 100 item chunks, then add them to db
for ($date = $startDate; $date <= $endDate; $date->modify("+1 day")) {
    $formattedDate = $date->format("Y-m-d");
    echo "\r" . $formattedDate;
    $baseUrl = $apiUrl . $formattedDate;
    $offset = 0;
    $totalDailyCount = 0;
    $isIncreasedFetchedCount = false;
    $movements = [];

    do {
        $url = $baseUrl . "&offset=" . $offset;
        $response = file_get_contents($url);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (is_array($data)) {
                if (!$isIncreasedFetchedCount) {
                    $totalDailyCount = $data["total_count"];
                    $fetchedCount += $totalDailyCount;
                }
//            $movements = array_merge($movements, $data["results"]);
                foreach ($data["results"] as $item) {
                    $movements[] = $item;
                }
                $offset += 100;
                $isIncreasedFetchedCount = true;
            } else {
                error_log("Invalid JSON response for date $formattedDate: $response");
            }
        } else {
            error_log("Failed to fetch data for date $formattedDate");
        }
    } while ($offset < $totalDailyCount);

    if (count($movements) > 0) {
        $eventDate = $movements[0]["data_evento"];
        $day = $movements[0]["giorno"];
        $zoneIdFrom = $movements[0]["area_from"];
        $zoneIdTo = $movements[0]["area_to"];
        $percentile50 = array_fill(0, 24, 0);
        $totPass = array_fill(0, 24, 0);

        foreach ($movements as $item) {
            if ($item["data_evento"] === $eventDate
                && $item["area_from"] === $zoneIdFrom
                && $item["area_to"] === $zoneIdTo) {
                $percentile50[$item["hour"]] = $item["percentile_50"];
                $totPass[$item["hour"]] = $item["tot_pass"];
            } else {
                $db->addMovement(
                    $eventDate,
                    $day,
                    $percentile50[0], $percentile50[1], $percentile50[2], $percentile50[3],
                    $percentile50[4], $percentile50[5], $percentile50[6], $percentile50[7],
                    $percentile50[8], $percentile50[9], $percentile50[10], $percentile50[11],
                    $percentile50[12], $percentile50[13], $percentile50[14], $percentile50[15],
                    $percentile50[16], $percentile50[17], $percentile50[18], $percentile50[19],
                    $percentile50[20], $percentile50[21], $percentile50[22], $percentile50[23],
                    $totPass[0], $totPass[1], $totPass[2], $totPass[3],
                    $totPass[4], $totPass[5], $totPass[6], $totPass[7],
                    $totPass[8], $totPass[9], $totPass[10], $totPass[11],
                    $totPass[12], $totPass[13], $totPass[14], $totPass[15],
                    $totPass[16], $totPass[17], $totPass[18], $totPass[19],
                    $totPass[20], $totPass[21], $totPass[22], $totPass[23],
                    $zoneIdTo,
                    $zoneIdFrom
                );
                $eventDate = $item["data_evento"];
                $day = $item["giorno"];
                $zoneIdFrom = $item["area_from"];
                $zoneIdTo = $item["area_to"];
                $percentile50 = array_fill(0, 24, 0);
                $totPass = array_fill(0, 24, 0);
            }
            $counter++;
        }
    }
}
//echo PHP_EOL . "Merged list count: " . count($movements) . PHP_EOL;
echo PHP_EOL . "Total number of elements added to db: " . $counter . PHP_EOL;
echo "Fetched list count: " . $fetchedCount . PHP_EOL;
//echo json_encode($mergedList, JSON_PRETTY_PRINT);
