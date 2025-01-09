<?php

class DatabaseHelper
{
    private mysqli $db;

    public function __construct(
        string $host = "localhost",
        string $username = "root",
        string $password = "",
        string $dbname = "bologna_wifi_map",
        string $port = "3306"
    )
    {
        $this->db = new mysqli($host, $username, $password, $dbname, $port);
        if ($this->db->connect_error) {
            die("Connection failed: " . $this->db->connect_error);
        }
    }

    public function __destruct()
    {
        $this->db->close();
    }

    public function query(string $query): false|array|null
    {
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function addArea(
        string $zoneId,
        string $zoneName,
        float  $latitude,
        float  $longitude
    ): bool
    {
        $query = "insert into areas(
                          zone_id, 
                          zone_name, 
                          geo_shape_type, 
                          geo_shape_geometry_type, 
                          geo_point_2d_lat, 
                          geo_point_2d_lon,
                          crowding_hour_offset,
                          attendance_hour_offset
                  ) 
                  values (
                          ?,
                          ?,
                          'Feature',
                          'Polygon',
                          ?,
                          ?,
                          0,
                          0
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssdd", $zoneId, $zoneName, $latitude, $longitude);
        return $stmt->execute();
    }

    public function addCoordinate(
        float  $latitude,
        float  $longitude,
        int    $order,
        string $zoneId
    ): bool
    {
        $query = "insert into coordinates(
                        latitude, 
                        longitude, 
                        `order`, 
                        zone_id
                  ) 
                  values (
                        ?,
                        ?,
                        ?,
                        ?
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ddis", $latitude, $longitude, $order, $zoneId);
        return $stmt->execute();
    }

    public function addMovement(
        string $date,
        string $day,
        int    $fiftiethPercentile00, int $fiftiethPercentile01, int $fiftiethPercentile02, int $fiftiethPercentile03,
        int    $fiftiethPercentile04, int $fiftiethPercentile05, int $fiftiethPercentile06, int $fiftiethPercentile07,
        int    $fiftiethPercentile08, int $fiftiethPercentile09, int $fiftiethPercentile10, int $fiftiethPercentile11,
        int    $fiftiethPercentile12, int $fiftiethPercentile13, int $fiftiethPercentile14, int $fiftiethPercentile15,
        int    $fiftiethPercentile16, int $fiftiethPercentile17, int $fiftiethPercentile18, int $fiftiethPercentile19,
        int    $fiftiethPercentile20, int $fiftiethPercentile21, int $fiftiethPercentile22, int $fiftiethPercentile23,
        int    $totPass00, int $totPass01, int $totPass02, int $totPass03,
        int    $totPass04, int $totPass05, int $totPass06, int $totPass07,
        int    $totPass08, int $totPass09, int $totPass10, int $totPass11,
        int    $totPass12, int $totPass13, int $totPass14, int $totPass15,
        int    $totPass16, int $totPass17, int $totPass18, int $totPass19,
        int    $totPass20, int $totPass21, int $totPass22, int $totPass23,
        string $zoneIdTo,
        string $zoneIdFrom
    ): bool
    {
        $query = "insert into movements(
                        date, 
                        day, 
                        percentile_50_00, percentile_50_01, percentile_50_02, percentile_50_03, 
                        percentile_50_04, percentile_50_05, percentile_50_06, percentile_50_07, 
                        percentile_50_08, percentile_50_09, percentile_50_10, percentile_50_11, 
                        percentile_50_12, percentile_50_13, percentile_50_14, percentile_50_15, 
                        percentile_50_16, percentile_50_17, percentile_50_18, percentile_50_19, 
                        percentile_50_20, percentile_50_21, percentile_50_22, percentile_50_23, 
                        tot_pass_00, tot_pass_01, tot_pass_02, tot_pass_03, 
                        tot_pass_04, tot_pass_05, tot_pass_06, tot_pass_07, 
                        tot_pass_08, tot_pass_09, tot_pass_10, tot_pass_11, 
                        tot_pass_12, tot_pass_13, tot_pass_14, tot_pass_15, 
                        tot_pass_16, tot_pass_17, tot_pass_18, tot_pass_19, 
                        tot_pass_20, tot_pass_21, tot_pass_22, tot_pass_23, 
                        zone_id_to, 
                        zone_id_from
                  ) 
                  values (
                        ?,
                        ?,
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?,
                        ?
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiss",
            $date,
            $day,
            $fiftiethPercentile00, $fiftiethPercentile01, $fiftiethPercentile02, $fiftiethPercentile03,
            $fiftiethPercentile04, $fiftiethPercentile05, $fiftiethPercentile06, $fiftiethPercentile07,
            $fiftiethPercentile08, $fiftiethPercentile09, $fiftiethPercentile10, $fiftiethPercentile11,
            $fiftiethPercentile12, $fiftiethPercentile13, $fiftiethPercentile14, $fiftiethPercentile15,
            $fiftiethPercentile16, $fiftiethPercentile17, $fiftiethPercentile18, $fiftiethPercentile19,
            $fiftiethPercentile20, $fiftiethPercentile21, $fiftiethPercentile22, $fiftiethPercentile23,
            $totPass00, $totPass01, $totPass02, $totPass03,
            $totPass04, $totPass05, $totPass06, $totPass07,
            $totPass08, $totPass09, $totPass10, $totPass11,
            $totPass12, $totPass13, $totPass14, $totPass15,
            $totPass16, $totPass17, $totPass18, $totPass19,
            $totPass20, $totPass21, $totPass22, $totPass23,
            $zoneIdTo,
            $zoneIdFrom
        );
        return $stmt->execute();
    }

    public function addCrowdingAttendance(
        string $date,
        string $day,
        int    $avgCrowding00, int $avgCrowding01, int $avgCrowding02, int $avgCrowding03,
        int    $avgCrowding04, int $avgCrowding05, int $avgCrowding06, int $avgCrowding07,
        int    $avgCrowding08, int $avgCrowding09, int $avgCrowding10, int $avgCrowding11,
        int    $avgCrowding12, int $avgCrowding13, int $avgCrowding14, int $avgCrowding15,
        int    $avgCrowding16, int $avgCrowding17, int $avgCrowding18, int $avgCrowding19,
        int    $avgCrowding20, int $avgCrowding21, int $avgCrowding22, int $avgCrowding23,
        int    $avgAttendance00, int $avgAttendance01, int $avgAttendance02, int $avgAttendance03,
        int    $avgAttendance04, int $avgAttendance05, int $avgAttendance06, int $avgAttendance07,
        int    $avgAttendance08, int $avgAttendance09, int $avgAttendance10, int $avgAttendance11,
        int    $avgAttendance12, int $avgAttendance13, int $avgAttendance14, int $avgAttendance15,
        int    $avgAttendance16, int $avgAttendance17, int $avgAttendance18, int $avgAttendance19,
        int    $avgAttendance20, int $avgAttendance21, int $avgAttendance22, int $avgAttendance23,
        string $zoneId
    ): bool
    {
        $query = "insert into crowding_attendance(
                        date, 
                        day, 
                        avg_crowding_00, avg_crowding_01, avg_crowding_02, avg_crowding_03, 
                        avg_crowding_04, avg_crowding_05, avg_crowding_06, avg_crowding_07, 
                        avg_crowding_08, avg_crowding_09, avg_crowding_10, avg_crowding_11, 
                        avg_crowding_12, avg_crowding_13, avg_crowding_14, avg_crowding_15, 
                        avg_crowding_16, avg_crowding_17, avg_crowding_18, avg_crowding_19, 
                        avg_crowding_20, avg_crowding_21, avg_crowding_22, avg_crowding_23, 
                        avg_attendance_00, avg_attendance_01, avg_attendance_02, avg_attendance_03, 
                        avg_attendance_04, avg_attendance_05, avg_attendance_06, avg_attendance_07, 
                        avg_attendance_08, avg_attendance_09, avg_attendance_10, avg_attendance_11, 
                        avg_attendance_12, avg_attendance_13, avg_attendance_14, avg_attendance_15, 
                        avg_attendance_16, avg_attendance_17, avg_attendance_18, avg_attendance_19, 
                        avg_attendance_20, avg_attendance_21, avg_attendance_22, avg_attendance_23, 
                        zone_id
                  )
                  values (
                        ?,
                        ?,
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "ssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiis",
            $date,
            $day,
            $avgCrowding00, $avgCrowding01, $avgCrowding02, $avgCrowding03,
            $avgCrowding04, $avgCrowding05, $avgCrowding06, $avgCrowding07,
            $avgCrowding08, $avgCrowding09, $avgCrowding10, $avgCrowding11,
            $avgCrowding12, $avgCrowding13, $avgCrowding14, $avgCrowding15,
            $avgCrowding16, $avgCrowding17, $avgCrowding18, $avgCrowding19,
            $avgCrowding20, $avgCrowding21, $avgCrowding22, $avgCrowding23,
            $avgAttendance00, $avgAttendance01, $avgAttendance02, $avgAttendance03,
            $avgAttendance04, $avgAttendance05, $avgAttendance06, $avgAttendance07,
            $avgAttendance08, $avgAttendance09, $avgAttendance10, $avgAttendance11,
            $avgAttendance12, $avgAttendance13, $avgAttendance14, $avgAttendance15,
            $avgAttendance16, $avgAttendance17, $avgAttendance18, $avgAttendance19,
            $avgAttendance20, $avgAttendance21, $avgAttendance22, $avgAttendance23,
            $zoneId
        );
        return $stmt->execute();
    }

    public function setH23CrowdingAttendance(
        int $avgCrowding23,
        int $avgAttendance23
    ): bool
    {
        $lastCrowdingAttendanceId = $this->getLastCrowdingAttendanceId();
        if ($lastCrowdingAttendanceId === null) {
            return false;
        }
        $query = "update crowding_attendance
                  set avg_crowding_23 = ?, avg_attendance_23 = ?
                  where crowding_attendance_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "iii",
            $avgCrowding23, $avgAttendance23,
            $lastCrowdingAttendanceId
        );
        return $stmt->execute();
    }

    public function getLastCrowdingAttendanceId(string $zoneId): int|null
    {
        $query = "select max(crowding_attendance_id)
                  from crowding_attendance
                  where zone_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $zoneId);
        $stmt->execute();
        $stmt->bind_result($lastCrowdingAttendanceId);
        $stmt->fetch();
        return $lastCrowdingAttendanceId;
    }

    public function getLastCrowdingAttendanceDate(): string|null
    {
        $query = "select max(date)
                  from crowding_attendance";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($lastDate);
        $stmt->fetch();
        return $lastDate;
    }

    public function getLastMovementsDate(): string|null
    {
        $query = "select max(date)
                  from movements";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $stmt->bind_result($lastDate);
        $stmt->fetch();
        return $lastDate;
    }

    public function setH22CrowdingAttendance(
        int    $avgCrowding22,
        int    $avgAttendance22,
        string $zoneId
    ): bool
    {
        $lastCrowdingAttendanceId = $this->getLastCrowdingAttendanceId($zoneId);
        if ($lastCrowdingAttendanceId === null) {
            return false;
        }
        $query = "update crowding_attendance
                  set avg_crowding_22 = ?, avg_attendance_22 = ?
                  where crowding_attendance_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param(
            "iii",
            $avgCrowding22, $avgAttendance22,
            $lastCrowdingAttendanceId
        );
        return $stmt->execute();
    }

    /** Returns empty array if nothing is found */
    public function getAllAreas(): array
    {
        $query = "select zone_id
                  from areas";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Returns empty array if nothing is found */
    public function getAllCrowdingAttendance(): array
    {
        $query = "select crowding_attendance_id
                  from crowding_attendance";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}