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

    /** Returns empty array if nothing is found */
    public function getAllAreas(): array
    {
        $query = "select zone_id
                  from areas";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getCrowdingHourOffset(string $zoneId): int
    {
        $query = "select crowding_hour_offset
                  from areas
                  where zone_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $zoneId);
        $stmt->execute();
        $stmt->bind_result($crowdingHourOffset);
        $stmt->fetch();
        return $crowdingHourOffset;
    }

    public function setCrowdingHourOffset(int $crowdingHourOffset, string $zoneId): bool
    {
        $query = "update areas
                  set crowding_hour_offset = ?
                  where zone_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $crowdingHourOffset, $zoneId);
        return $stmt->execute();
    }

    public function getAttendanceHourOffset(string $zoneId): int
    {
        $query = "select crowding_hour_offset
                  from areas
                  where zone_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("i", $zoneId);
        $stmt->execute();
        $stmt->bind_result($attendanceHourOffset);
        $stmt->fetch();
        return $attendanceHourOffset;
    }

    public function setAttendanceHourOffset(int $attendanceHourOffset, string $zoneId): bool
    {
        $query = "update areas
                  set attendance_hour_offset = ?
                  where zone_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("is", $attendanceHourOffset, $zoneId);
        return $stmt->execute();
    }
}