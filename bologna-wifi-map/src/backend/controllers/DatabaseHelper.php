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
}