<?php

trait Areas
{
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
                          geo_point_2d_lat, 
                          geo_point_2d_lon
                  ) 
                  values (
                          ?,
                          ?,
                          ?,
                          ?
                  )";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("ssdd", $zoneId, $zoneName, $latitude, $longitude);
        return $stmt->execute();
    }

    /** Get all areas together with their coordinate list */
    public function getAllAreas(): array
    {
        $zoneIdList = $this->getAllZoneIds();
        $query = "select zone_name, geo_point_2d_lat, geo_point_2d_lon
                  from areas
                  where zone_id = ?";
        $areas = [];
        foreach ($zoneIdList as $zoneId) {
            $zoneId = $zoneId["zone_id"];
            $coordinates = $this->getCoordinates($zoneId);
            $stmt = $this->db->prepare($query);
            $stmt->bind_param("s", $zoneId);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($zoneName, $latitude, $longitude);
            $stmt->fetch();
            $areas[] = [
                "zone_id" => $zoneId,
                "zone_name" => $zoneName,
                "latitude" => $latitude,
                "longitude" => $longitude,
                "coordinates" => $coordinates
            ];
        }
        return $areas;
    }

    /** Returns empty array if nothing is found */
    public function getAllZoneIds(): array
    {
        $query = "select zone_id
                  from areas";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}