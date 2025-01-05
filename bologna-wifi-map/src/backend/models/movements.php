<?php
header('Content-Type: application/json');
// First data from April 1, 2021
$apiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?order_by=data_evento%20asc&refine=data_evento%3A";
$totalCountUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "bolognawifi-matrice-spostamenti/records?order_by=data_evento%20asc&limit=0";
//$startDate = new DateTime("2021-04-01");
$startDate = new DateTime("2024-12-01");    // debug
$endDate = new DateTime();
$mergedList = [];
$totalCount = 0;
$fetchedCount = 0;

function fetch_api_data($url): bool|string
{
    $response = file_get_contents($url);
    if ($response === false) {
        error_log("Failed to fetch data from URL: $url");
        return false;
    }
    return $response;

//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, $url);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return the response as a string
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // Verify SSL certificates
//    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
//
//    $response = curl_exec($ch);
//
//    if (curl_errno($ch)) {
//        error_log('cURL error: ' . curl_error($ch));
//        curl_close($ch);
//        return false;
//    }
//
//    curl_close($ch);
//    return $response;
}

$response = fetch_api_data($totalCountUrl);
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
    $response = fetch_api_data($url);

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

echo PHP_EOL . $fetchedCount . PHP_EOL;
echo json_encode($mergedList, JSON_PRETTY_PRINT);