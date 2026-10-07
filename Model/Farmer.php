<?php

require_once __DIR__ . '/Database.php';

class Farmer
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE FARMER
    // ==========================================

    public function create(
        int $userId,
        string $farmName,
        string $location,
        string $address
    ): bool {

        $sql = "
            INSERT INTO farmers
            (
                user_id,
                farm_name,
                location,
                address
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "isss",
            $userId,
            $farmName,
            $location,
            $address
        );

        return $stmt->execute();
    }


    // ==========================================
    // FIND BY USER ID
    // ==========================================

    public function findByUserId(int $userId): ?array
    {
        $sql = "
            SELECT
                f.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role
            FROM farmers f
            INNER JOIN users u
                ON f.user_id = u.user_id
            WHERE f.user_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $userId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // FIND BY FARMER ID
    // ==========================================

    public function findById(int $farmerId): ?array
    {
        $sql = "
            SELECT
                f.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM farmers f
            INNER JOIN users u
                ON f.user_id = u.user_id
            WHERE f.farmer_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $farmerId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // GET ALL FARMERS
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                f.farmer_id,
                f.user_id,
                f.farm_name,
                f.location,
                f.address,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.created_at
            FROM farmers f
            INNER JOIN users u
                ON f.user_id = u.user_id
            ORDER BY f.farmer_id DESC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // ==========================================
    // UPDATE FARMER
    // ==========================================

    public function update(
        int $farmerId,
        string $farmName,
        string $location,
        string $address
    ): bool {

        $sql = "
            UPDATE farmers
            SET
                farm_name = ?,
                location = ?,
                address = ?
            WHERE farmer_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssi",
            $farmName,
            $location,
            $address,
            $farmerId
        );

        return $stmt->execute();
    }


    // ==========================================
    // COUNT FARMERS
    // ==========================================

    public function countAll(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM farmers
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}