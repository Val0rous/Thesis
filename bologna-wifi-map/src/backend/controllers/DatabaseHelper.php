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
                          geo_point_2d_lon
                  ) 
                  values (
                          ?,
                          ?,
                          'Feature',
                          'Polygon',
                          ?,
                          ?
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
        int    $fiftiethPercentile00,
        int    $fiftiethPercentile01,
        int    $fiftiethPercentile02,
        int    $fiftiethPercentile03,
        int    $fiftiethPercentile04,
        int    $fiftiethPercentile05,
        int    $fiftiethPercentile06,
        int    $fiftiethPercentile07,
        int    $fiftiethPercentile08,
        int    $fiftiethPercentile09,
        int    $fiftiethPercentile10,
        int    $fiftiethPercentile11,
        int    $fiftiethPercentile12,
        int    $fiftiethPercentile13,
        int    $fiftiethPercentile14,
        int    $fiftiethPercentile15,
        int    $fiftiethPercentile16,
        int    $fiftiethPercentile17,
        int    $fiftiethPercentile18,
        int    $fiftiethPercentile19,
        int    $fiftiethPercentile20,
        int    $fiftiethPercentile21,
        int    $fiftiethPercentile22,
        int    $fiftiethPercentile23,
        int    $totSteps00,
        int    $totSteps01,
        int    $totSteps02,
        int    $totSteps03,
        int    $totSteps04,
        int    $totSteps05,
        int    $totSteps06,
        int    $totSteps07,
        int    $totSteps08,
        int    $totSteps09,
        int    $totSteps10,
        int    $totSteps11,
        int    $totSteps12,
        int    $totSteps13,
        int    $totSteps14,
        int    $totSteps15,
        int    $totSteps16,
        int    $totSteps17,
        int    $totSteps18,
        int    $totSteps19,
        int    $totSteps20,
        int    $totSteps21,
        int    $totSteps22,
        int    $totSteps23,
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
                        tot_steps_00, tot_steps_01, tot_steps_02, tot_steps_03, 
                        tot_steps_04, tot_steps_05, tot_steps_06, tot_steps_07, 
                        tot_steps_08, tot_steps_09, tot_steps_10, tot_steps_11, 
                        tot_steps_12, tot_steps_13, tot_steps_14, tot_steps_15, 
                        tot_steps_16, tot_steps_17, tot_steps_18, tot_steps_19, 
                        tot_steps_20, tot_steps_21, tot_steps_22, tot_steps_23, 
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
            $totSteps00, $totSteps01, $totSteps02, $totSteps03,
            $totSteps04, $totSteps05, $totSteps06, $totSteps07,
            $totSteps08, $totSteps09, $totSteps10, $totSteps11,
            $totSteps12, $totSteps13, $totSteps14, $totSteps15,
            $totSteps16, $totSteps17, $totSteps18, $totSteps19,
            $totSteps20, $totSteps21, $totSteps22, $totSteps23,
            $zoneIdTo,
            $zoneIdFrom
        );
        return $stmt->execute();
    }
}