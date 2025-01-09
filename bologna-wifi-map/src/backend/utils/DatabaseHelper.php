<?php

require_once("../models/Areas.php");
require_once("../models/Coordinates.php");
require_once("../models/Movements.php");
require_once("../models/CrowdingAttendance.php");

class DatabaseHelper
{
    use Areas;
    use Coordinates;
    use Movements;
    use CrowdingAttendance;

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