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
}