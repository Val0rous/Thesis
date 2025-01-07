<?php
header('Content-Type: application/json');
require_once "../controllers/DatabaseHelper.php";
$zoneIdsFromCrowdingApiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affollamento/records?select=codice_zona&group_by=codice_zona&order_by=data%20asc&limit=100";
$zoneIdsFromAttendanceApiUrl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affluenza/records?select=codice_zona&group_by=codice_zona&order_by=data%20asc";
$crowdingList = [];
$attendanceList = [];
$zoneIds = [];
$areaFromCrowdingApiURl = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affollamento/records?select=codice_zona%2C%20nome_zona%2C%20geo_shape%2C%20geo_point_2d&order_by=data%20asc&limit=1&refine=codice_zona%3A";
$areaFromAttendanceApiURL = "https://opendata.comune.bologna.it/api/explore/v2.1/catalog/datasets/"
    . "iperbole-wifi-affluenza/records?select=codice_zona%2C%20nome_zona%2C%20geo_shape%2C%20geo_point_2d&order_by=data%20asc&limit=1&refine=codice_zona%3A";
$areas = [];
$counter = 0;

// Fetch all distinct zone IDs - we're using Crowding APIs but Attendance would work just as well
$crowdingResponse = file_get_contents($zoneIdsFromCrowdingApiUrl);
if ($crowdingResponse !== false) {
    $crowdingList = array_column(json_decode($crowdingResponse, true)["results"], "codice_zona");
} else {
    error_log("Failed to fetch data for zone IDs from crowding API");
}
echo "Total number of fetched zone IDs: " . count($crowdingList);
echo PHP_EOL;

// Fetch area and coordinates for each zone ID
foreach ($crowdingList as $zoneId) {
    echo "\r" . $zoneId;
    $url = $areaFromCrowdingApiURl . urlencode($zoneId);
    $response = file_get_contents($url);

    if ($response !== false) {
        $data = json_decode($response, true)["results"][0];
        $flattenedData = [
            "codice_zona" => $data["codice_zona"],
            "nome_zona" => $data["nome_zona"],
            "coordinates" => $data["geo_shape"]["geometry"]["coordinates"][0],
            "geo_point_2d" => $data["geo_point_2d"],
        ];
        $areas[$zoneId] = $flattenedData;
        $counter += count($flattenedData["coordinates"]);
    }
}
// echo PHP_EOL . json_encode($areas, JSON_PRETTY_PRINT);
echo PHP_EOL . "Total number of fetched areas: " . count($areas) . PHP_EOL;

// Add to db
$db = new DatabaseHelper();
foreach ($areas as $area) {
    $db->addArea($area["codice_zona"], $area["nome_zona"], $area["geo_point_2d"]["lat"], $area["geo_point_2d"]["lon"]);
    foreach ($area["coordinates"] as $index => $coordinate) {
        $db->addCoordinate($coordinate[1], $coordinate[0], $index, $area["codice_zona"]);
    }
}

echo "Total number of fetched coordinates: " . $counter . PHP_EOL;

//$attendanceResponse = file_get_contents($zoneIdsFromAttendanceApiUrl);
//if ($attendanceResponse !== false) {
//    $attendanceList = array_column(json_decode($attendanceResponse, true)["results"], "codice_zona");
//} else {
//    error_log("Failed to fetch data for zone IDs from attendance API");
//}
//echo json_encode(count($attendanceList), JSON_PRETTY_PRINT);
//if ($crowdingList === $attendanceList) {
//    echo PHP_EOL . "Both arrays are the same";  // Turns out they are, so let's keep just one
//}