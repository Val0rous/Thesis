<?php

trait CrowdingAttendance
{
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

    public function setH23CrowdingAttendance(
        int    $avgCrowding23,
        int    $avgAttendance23,
        string $zoneId
    ): bool
    {
        $lastCrowdingAttendanceId = $this->getLastCrowdingAttendanceId($zoneId);
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
}