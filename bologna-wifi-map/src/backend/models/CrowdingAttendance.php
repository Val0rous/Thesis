<?php

trait CrowdingAttendance
{
    /**
     * @param string $date
     * @param string $day
     * @param int[] $avgCrowding
     * @param int[] $avgAttendance
     * @param string $zoneId
     * @return bool
     */
    public function addCrowdingAttendance(
        string $date,
        string $day,
        array  $avgCrowding,
        array  $avgAttendance,
        string $zoneId
    ): bool
    {
        if (count($avgCrowding) !== 24) {
            throw new InvalidArgumentException("avgCrowding array length must be 24. Current length: " . count($avgCrowding));
        }
        if (count($avgAttendance) !== 24) {
            throw new InvalidArgumentException("avgAttendance array length must be 24. Current length: " . count($avgAttendance));
        }
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
            $avgCrowding[0], $avgCrowding[1], $avgCrowding[2], $avgCrowding[3],
            $avgCrowding[4], $avgCrowding[5], $avgCrowding[6], $avgCrowding[7],
            $avgCrowding[8], $avgCrowding[9], $avgCrowding[10], $avgCrowding[11],
            $avgCrowding[12], $avgCrowding[13], $avgCrowding[14], $avgCrowding[15],
            $avgCrowding[16], $avgCrowding[17], $avgCrowding[18], $avgCrowding[19],
            $avgCrowding[20], $avgCrowding[21], $avgCrowding[22], $avgCrowding[23],
            $avgAttendance[0], $avgAttendance[1], $avgAttendance[2], $avgAttendance[3],
            $avgAttendance[4], $avgAttendance[5], $avgAttendance[6], $avgAttendance[7],
            $avgAttendance[8], $avgAttendance[9], $avgAttendance[10], $avgAttendance[11],
            $avgAttendance[12], $avgAttendance[13], $avgAttendance[14], $avgAttendance[15],
            $avgAttendance[16], $avgAttendance[17], $avgAttendance[18], $avgAttendance[19],
            $avgAttendance[20], $avgAttendance[21], $avgAttendance[22], $avgAttendance[23],
            $zoneId
        );
        return $stmt->execute();
    }

    public function getCrowdingAttendance(string $date): array
    {
        $query = "select zone_id,
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
                  avg_attendance_20, avg_attendance_21, avg_attendance_22, avg_attendance_23
                  from crowding_attendance
                  where date = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $crowding = [];
        $attendance = [];
        while ($row = $result->fetch_assoc()) {
            $zoneId = $row["zone_id"];
            $crowding[$zoneId] = array_map(
                fn($hour) => (int)$row[sprintf("avg_crowding_%02d", $hour)],
                range(0, 23)
            );
            $attendance[$zoneId] = array_map(
                fn($hour) => (int)$row[sprintf("avg_attendance_%02d", $hour)],
                range(0, 23)
            );
        }
        return [
            "crowding" => $crowding,
            "attendance" => $attendance
        ];
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
}