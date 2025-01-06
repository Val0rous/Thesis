<?php
header('Content-Type: application/json');
// First data from April 1, 2021
$apiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?select=data_evento%2C%20hour%2C%20giorno%2C%20percentile_50%2C%20tot_pass%2C%20area_from%2C%20area_to&order_by=area_from%2C%20area_to%2C%20hour%20asc&refine=data_evento%3A";
$totalCountUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?order_by=data_evento%20asc&limit=0";
$startDate = new DateTime("2021-04-01");
//$startDate = new DateTime("2025-01-03");    // debug
$endDate = new DateTime();
$mergedList = [];
$totalCount = 0;
$fetchedCount = 0;

$response = file_get_contents($totalCountUrl);
if ($response !== false) {
    $totalCount = json_decode($response, true)["total_count"];
} else {
    error_log("Failed to fetch data for total count");
}
echo $totalCount . PHP_EOL;

for ($date = $startDate; $date <= $endDate; $date->modify("+1 day")) {
    $formattedDate = $date->format("Y-m-d");
    echo "\r" . $formattedDate;
    $url = $apiUrl . $formattedDate;
    $response = file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true);
        if (is_array($data)) {
            $fetchedCount += $data["total_count"];
            $mergedList = array_merge($mergedList, $data["results"]);
        } else {
            error_log("Invalid JSON response for date $formattedDate: $response");
        }
    } else {
        error_log("Failed to fetch data for date $formattedDate");
    }
}
echo count($mergedList) . PHP_EOL;
echo PHP_EOL . $fetchedCount . PHP_EOL;
//echo json_encode($mergedList, JSON_PRETTY_PRINT);

