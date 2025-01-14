<?php

trait Coordinates
{
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

    /** Get all coordinates from an area in the right order, as a list of [lat,lng] */
    public function getCoordinates(string $zoneId): array
    {
        $query = "select latitude, longitude
                  from coordinates
                  where zone_id = ?
                  order by `order`";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $zoneId);
        $stmt->execute();
        $stmt->bind_result($latitude, $longitude);
        $coordinates = [];
        while ($stmt->fetch()) {
            $coordinates[] = [(float)$latitude, (float)$longitude];
        }
        return $coordinates;
    }
}