<?php
header('Content-Type: application/json');

function fetchCrowdingAttendance(array $urls, DatabaseHelper $db): void
{
    $startTime = microtime(true);
    echo "Fetching crowding and attendance..." . PHP_EOL;
    // First data from January 1, 2024
    $crowdingApiUrl = $urls["crowding_api"];
    $crowdingTotalCountUrl = $urls["crowding_total_count"];
    $attendanceApiUrl = $urls["attendance_api"];
    $attendanceTotalCountUrl = $urls["attendance_total_count"];
    $lastDate = $db->getLastCrowdingAttendanceDate();
    $lastDate = ($lastDate !== null) ? new DateTime($lastDate) : null;
    $crowdingStartDate = new DateTime("2024-01-15");    // Affollamento
    $attendanceStartDate = new DateTime("2024-01-01");  // Affluenza
    $startDate = ($lastDate !== null) ? $lastDate->modify("+1 day") : $attendanceStartDate;
    //$startDate = new DateTime("2025-01-01");    // debug
    $endDate = new DateTime();
    $crowdingTotalCount = 0;
    $crowdingFetchedCount = 0;
    $crowdingCounter = 0;
    $attendanceTotalCount = 0;
    $attendanceFetchedCount = 0;
    $attendanceCounter = 0;
    $where = "&where=data%20%3E%3D%20%22"
        . $startDate->format("Y-m-d")
        . "%22%20and%20data%20%3C%3D%20%22"
        . $endDate->format("Y-m-d")
        . "%22";
    $areas = $db->getAllAreas();

    $crowdingResponse = file_get_contents($crowdingTotalCountUrl . $where);
    if ($crowdingResponse !== false) {
        $crowdingTotalCount = json_decode($crowdingResponse, true)["total_count"];
    } else {
        error_log("Failed to fetch data for crowding total count");
    }
    echo "Total count of crowding to add: " . $crowdingTotalCount . PHP_EOL;

    $attendanceResponse = file_get_contents($attendanceTotalCountUrl . $where);
    if ($attendanceResponse !== false) {
        $attendanceTotalCount = json_decode($attendanceResponse, true)["total_count"];
    } else {
        error_log("Failed to fetch data for attendance total count");
    }
    echo "Total count of attendance to add: " . $attendanceTotalCount . PHP_EOL;

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

    if ($attendanceTotalCount > 0 || $crowdingTotalCount > 0) {
        // Fetch Crowding and Attendance data day by day in 100 item chunks, then add them to db
        for ($date = $startDate; $date <= $endDate; $date->modify("+1 day")) {
            $formattedDate = $date->format("Y-m-d");
            echo "\r" . $formattedDate;
            $crowdingBaseUrl = $crowdingApiUrl . $formattedDate . $where;
            $attendanceBaseUrl = $attendanceApiUrl . $formattedDate . $where;
            $crowdingOffset = 0;
            $attendanceOffset = 0;
            $crowdingTotalDailyCount = 0;
            $attendanceTotalDailyCount = 0;
            $crowdingIsIncreasedFetchedCount = false;
            $attendanceIsIncreasedFetchedCount = false;
            $crowding = new SplQueue();
            $attendance = new SplQueue();

            $isCrowding = ($date >= $crowdingStartDate);

            if ($isCrowding) {
                // Fetch Crowding
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
                            if ($crowdingTotalDailyCount > 0) {
                                echo "\r" . $formattedDate . " - " . ($crowdingOffset / 100 + 1) . "/" . (ceil($crowdingTotalDailyCount / 100)) . " - crowding";
                            }
                            foreach ($crowdingData["results"] as $item) {
                                $crowding->enqueue($item);
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

            // Fetch Attendance
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
                        if ($attendanceTotalDailyCount > 0) {
                            echo "\r" . $formattedDate . " - " . ($attendanceOffset / 100 + 1) . "/" . (ceil($attendanceTotalDailyCount / 100)) . " - attendance";
                        }
                        foreach ($attendanceData["results"] as $item) {
                            $attendance->enqueue($item);
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
                $eventDate = explode("T", $attendance->bottom()["data"])[0];
                $day = $attendance->bottom()["giorno"];
                $zoneIdList = new ArrayObject();
                foreach ($areas as $area) {
                    $zoneIdList->append($area);
                }

                while ($zoneIdList->count() > 0) {
                    $zoneId = $attendance->bottom()["codice_zona"];
                    // Necessary as zoneIds are not in the exact order as database
                    foreach ($zoneIdList as $key => $item) {
                        if ($item["zone_id"] === $zoneId) {
                            // Remove current zoneId from list
                            unset($zoneIdList[$key]);
                            break;
                        }
                    }
                    $avgAttendance = array_fill(0, 24, 0);
                    $avgCrowding = array_fill(0, 24, 0);

                    while (!$attendance->isEmpty()
                        && $attendance->bottom()["codice_zona"] === $zoneId) {
                        // Add items to array
                        $index = extractHourFromTimestamp($attendance->bottom()["data"]);
                        $avgAttendance[$index] = max($attendance->bottom()["affluenza_media"], $avgAttendance[$index]);
                        $attendance->dequeue();
                        $attendanceCounter++;
                    }

                    if ($isCrowding) {
                        while (!$crowding->isEmpty()
                            && $crowding->bottom()["codice_zona"] === $zoneId) {
                            // Add items to array
                            $index = extractHourFromTimestamp($crowding->bottom()["data"]);
                            $avgCrowding[$index] = max($crowding->bottom()["affollamento_medio"], $avgCrowding[$index]);
                            $crowding->dequeue();
                            $crowdingCounter++;
                        }
                    }

                    // TODO: always use 24 values and place them in array based on the hour you get from date string
                    // TODO: if there are 23 values, fill the missing one with zero
                    // TODO: if there are 25 values, keep the max of the two values of the hour having double entries
                    // TODO: remove indexes from database, it's much simpler this way
                    // TODO: this is because basically all values from extra entries are just zeros

                    $db->addCrowdingAttendance(
                        $eventDate,
                        $day,
                        $avgCrowding,
                        $avgAttendance,
                        $zoneId
                    );
                }
            }
        }
    }
    echo PHP_EOL;
    echo "Total number of crowding elements added to db: " . $crowdingCounter . PHP_EOL;
    echo "Total number of attendance elements added to db: " . $attendanceCounter . PHP_EOL;
    echo "Fetched crowding list count: " . $crowdingFetchedCount . PHP_EOL;
    echo "Fetched attendance list count: " . $attendanceFetchedCount . PHP_EOL;
    $endTime = microtime(true);
    $elapsedTime = $endTime - $startTime;
    echo "Elapsed time: " . convertToReadableTime($elapsedTime) . PHP_EOL;
    echo PHP_EOL;
}