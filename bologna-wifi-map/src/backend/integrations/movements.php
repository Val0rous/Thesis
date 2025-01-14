<?php
header('Content-Type: application/json');

function fetchMovements(array $urls, DatabaseHelper $db): void
{
    $startTime = microtime(true);
    echo "Fetching movements..." . PHP_EOL;
    // First data from April 1, 2021
    $apiUrl = $urls["movements_api"];
    $totalCountUrl = $urls["movements_total_count"];
    $lastDate = $db->getLastMovementsDate();
    $lastDate = ($lastDate !== null) ? new DateTime($lastDate) : null;
    $startDate = ($lastDate !== null) ? $lastDate->modify("+1 day") : new DateTime("2021-04-01");
    //$startDate = new DateTime("2024-12-01");    // debug
    $endDate = new DateTime();
    $totalCount = 0;
    $fetchedCount = 0;
    $counter = 0;
    $where = "&where=data_evento%20%3E%3D%20%22"
        . $startDate->format("Y-m-d")
        . "%22%20and%20data_evento%20%3C%3D%20%22"
        . $endDate->format("Y-m-d")
        . "%22";

    $response = file_get_contents($totalCountUrl . $where);
    if ($response !== false) {
        $totalCount = json_decode($response, true)["total_count"];
    } else {
        error_log("Failed to fetch data for movements total count");
    }
    echo "Total count of movements to add: " . $totalCount . PHP_EOL;

    if ($totalCount > 0) {
        // Fetch data day by day in 100 item chunks, then add them to db
        for ($date = $startDate; $date <= $endDate; $date->modify("+1 day")) {
            $formattedDate = $date->format("Y-m-d");
            echo "\r" . $formattedDate;
            $baseUrl = $apiUrl . $formattedDate . $where;
            $offset = 0;
            $totalDailyCount = 0;
            $isIncreasedFetchedCount = false;
            $movements = new SplQueue();

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
                        if ($totalDailyCount > 0) {
                            echo "\r" . $formattedDate . " - " . ($offset / 100 + 1) . "/" . (ceil($totalDailyCount / 100));
                        }
                        //              $movements = array_merge($movements, $data["results"]);
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
                $eventDate = $movements->bottom()["data_evento"];
                $day = $movements->bottom()["giorno"];

                while (!$movements->isEmpty()) {
                    $zoneIdFrom = $movements->bottom()["area_from"];
                    $zoneIdTo = $movements->bottom()["area_to"];
                    $percentile50 = array_fill(0, 24, 0);
                    $totPass = array_fill(0, 24, 0);

                    while (!$movements->isEmpty()
                        && $movements->bottom()["area_from"] === $zoneIdFrom
                        && $movements->bottom()["area_to"] === $zoneIdTo) {
                        // Add items to array
                        $item = $movements->bottom();
                        $percentile50[$item["hour"]] = $item["percentile_50"];
                        $totPass[$item["hour"]] = $item["tot_pass"];
                        $movements->dequeue();
                        $counter++;
                    }

                    $db->addMovement(
                        $eventDate,
                        $day,
                        $percentile50,
                        $totPass,
                        $zoneIdTo,
                        $zoneIdFrom
                    );
                }

//                foreach ($movements as $item) {
//                    if ($item["data_evento"] === $eventDate
//                        && $item["area_from"] === $zoneIdFrom
//                        && $item["area_to"] === $zoneIdTo) {
//                        $percentile50[$item["hour"]] = $item["percentile_50"];
//                        $totPass[$item["hour"]] = $item["tot_pass"];
//                    } else {
//                        $db->addMovement(
//                            $eventDate,
//                            $day,
//                            $percentile50,
//                            $totPass,
//                            $zoneIdTo,
//                            $zoneIdFrom
//                        );
//                        $eventDate = $item["data_evento"];
//                        $day = $item["giorno"];
//                        $zoneIdFrom = $item["area_from"];
//                        $zoneIdTo = $item["area_to"];
//                        $percentile50 = array_fill(0, 24, 0);
//                        $totPass = array_fill(0, 24, 0);
//                    }
//                    $counter++;
//                }
            }
        }
    }
    //echo PHP_EOL . "Merged list count: " . count($movements) . PHP_EOL;
    echo PHP_EOL . "Total number of elements added to db: " . $counter . PHP_EOL;
    echo "Fetched list count: " . $fetchedCount . PHP_EOL;
    //echo json_encode($mergedList, JSON_PRETTY_PRINT);
    $endTime = microtime(true);
    $elapsedTime = $endTime - $startTime;
    echo "Elapsed time: " . convertToReadableTime($elapsedTime) . PHP_EOL;
    echo PHP_EOL;
}
