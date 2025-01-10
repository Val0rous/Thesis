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
            $crowding = [];
            $attendance = [];

            $isCrowding = ($date >= $crowdingStartDate);

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
                            if ($crowdingTotalDailyCount > 0) {
                                echo "\r" . $formattedDate . " - " . ($crowdingOffset / 100 + 1) . "/" . (ceil($crowdingTotalDailyCount / 100));
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
                        if ($attendanceTotalDailyCount > 0) {
                            echo "\r" . $formattedDate . " - " . ($attendanceOffset / 100 + 1) . "/" . (ceil($attendanceTotalDailyCount / 100));
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
                // Adding last element in both arrays so loop doesn't end prematurely
                $attendance[] = [
                    "data" => "",
                    "giorno" => "",
                    "codice_zona" => "",
                    "affluenza_media" => -1
                ];
                $crowding[] = [
                    "data" => "",
                    "giorno" => "",
                    "codice_zona" => "",
                    "affollamento_medio" => -1
                ];
                $eventDate = explode("T", $attendance[0]["data"])[0];
                $day = $attendance[0]["giorno"];
                $zoneId = $attendance[0]["codice_zona"];
                $avgAttendance = array_fill(0, 24, 0);
                $avgCrowding = array_fill(0, 24, 0);
                $attendanceIndex = 0;
                $crowdingIndex = 0;
                $attendanceHourOffset = $db->getAttendanceHourOffset($zoneId);
                $crowdingHourOffset = $db->getCrowdingHourOffset($zoneId);

                foreach ($attendance as $attendanceItem) {
                    if (explode("T", $attendanceItem["data"])[0] === $eventDate
                        && $attendanceItem["codice_zona"] === $zoneId) {
                        $avgAttendance[$attendanceIndex++] = $attendanceItem["affluenza_media"];
                    } else {
                        if (count($crowding) > 0 && $isCrowding) {
                            foreach ($crowding as $crowdingItem) {
                                if (explode("T", $crowdingItem["data"])[0] === $eventDate
                                    && $crowdingItem["codice_zona"] === $zoneId) {
                                    $avgCrowding[$crowdingIndex++] = $crowdingItem["affollamento_medio"];
                                } else {
                                    $attendanceHourOffsetDelta = ($attendanceIndex - 24);
                                    if ($attendanceHourOffsetDelta !== 0) {
                                        $db->setAttendanceHourOffset($attendanceHourOffset + $attendanceHourOffsetDelta, $zoneId);
                                        echo "Attendance Hour Offset changed on $eventDate to: " . ($attendanceHourOffset + $attendanceHourOffsetDelta) . PHP_EOL;
                                    }
                                    $attendanceH22 = null;
                                    $attendanceH23 = null;
                                    if ($attendanceHourOffset === -1) {
                                        $attendanceH23 = $attendance[0];
                                        array_shift($attendance);
                                    } else if ($attendanceHourOffset === -2) {
                                        $attendanceH22 = $attendance[0];
                                        $attendanceH23 = $attendance[1];
                                        array_splice($attendance, 0, 2);
                                    }

                                    $crowdingHourOffsetDelta = ($crowdingIndex - 24);
                                    if ($crowdingHourOffsetDelta !== 0) {
                                        $db->setCrowdingHourOffset($crowdingHourOffset + $crowdingHourOffsetDelta, $zoneId);
                                        echo "Crowding Hour Offset changed on $eventDate to: " . ($crowdingHourOffset + $crowdingHourOffsetDelta) . PHP_EOL;
                                    }
                                    $crowdingH22 = null;
                                    $crowdingH23 = null;
                                    if ($crowdingOffset === -1) {
                                        $crowdingH23 = $crowding[0];
                                        array_shift($crowding);
                                    } else if ($crowdingOffset === -2) {
                                        $crowdingH22 = $crowding[0];
                                        $crowdingH23 = $crowding[1];
                                        array_splice($crowding, 0, 2);
                                    }

                                    // Update last 1 or 2 elements of last entry of respective zoneId
                                    if ($attendanceH22 !== null && $crowdingH22 !== null) {
                                        $db->setH22CrowdingAttendance($crowdingH22, $attendanceH22, $zoneId);
                                        $attendanceH22 = null;
                                        $crowdingH22 = null;
                                    }
                                    if ($attendanceH23 !== null && $crowdingH23 !== null) {
                                        $db->setH23CrowdingAttendance($crowdingH23, $attendanceH23, $zoneId);
                                        $attendanceH23 = null;
                                        $crowdingH23 = null;
                                    }

                                    $db->addCrowdingAttendance(
                                        $eventDate,
                                        $day,
                                        $avgCrowding,
                                        $avgAttendance,
                                        $zoneId
                                    );
                                    $eventDate = explode("T", $attendanceItem["data"])[0];
                                    $day = $attendanceItem["giorno"];
                                    $zoneId = $attendanceItem["codice_zona"];
                                    $avgAttendance = array_fill(0, 24, 0);
                                    $avgCrowding = array_fill(0, 24, 0);
                                    $attendanceIndex = 0;
                                    $crowdingIndex = 0;
                                    $avgAttendance[$attendanceIndex++] = $attendanceItem["affluenza_media"];
                                    $avgCrowding[$crowdingIndex++] = $crowdingItem["affollamento_medio"];
                                    $attendanceHourOffset = $db->getAttendanceHourOffset($zoneId);
                                    $crowdingHourOffset = $db->getCrowdingHourOffset($zoneId);
                                }
                            }
                            $crowdingCounter++;
                        } else {
                            // No crowding, only occurs from Jan 1, 2024 to Jan 14, 2024
                            $db->addCrowdingAttendance(
                                $eventDate,
                                $day,
                                $avgCrowding,
                                $avgAttendance,
                                $zoneId
                            );
                            $eventDate = explode("T", $attendanceItem["data"])[0];
                            $day = $attendanceItem["giorno"];
                            $zoneId = $attendanceItem["codice_zona"];
                            $avgAttendance = array_fill(0, 24, 0);
                            $attendanceIndex = 0;
                            $avgAttendance[$attendanceIndex++] = $attendanceItem["affluenza_media"];
                        }
                    }
                    $attendanceCounter++;
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