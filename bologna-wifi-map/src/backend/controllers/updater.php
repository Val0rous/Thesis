<?php
header('Content-Type: application/json');
require_once("../utils/DatabaseHelper.php");
require_once("../integrations/areas_coordinates.php");
require_once("../integrations/movements.php");
require_once("../integrations/crowding_attendance.php");
require_once("../utils/utils.php");
require_once("../vendor/autoload.php");

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;

$urlsFilePath = "../routes/urls.json";
if (!file_exists($urlsFilePath)) {
    die("The file $urlsFilePath does not exist.");
}
$urls = json_decode(file_get_contents($urlsFilePath), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Failed to decode JSON: " . json_last_error_msg());
}

$db = new DatabaseHelper();

fetchAreas($urls, $db);
fetchMovements($urls, $db);
fetchCrowdingAttendance($urls, $db);