<?php

require_once __DIR__ . '/Database.php';

class DeliveryMan
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE
    // ==========================================

    public function create(
        int $userId,
        string $vehicleType,
        string $vehicleNumber
    ): bool {

        $sql = "
            INSERT INTO delivery_men
            (
                user_id,
                vehicle_type,
                vehicle_number
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iss",
            $userId,
            $vehicleType,
            $vehicleNumber
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
                d.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role
            FROM delivery_men d
            INNER JOIN users u
                ON d.user_id = u.user_id
            WHERE d.user_id = ?
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
    // FIND BY DELIVERY MAN ID
    // ==========================================

    public function findById(int $deliveryManId): ?array
    {
        $sql = "
            SELECT
                d.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM delivery_men d
            INNER JOIN users u
                ON d.user_id = u.user_id
            WHERE d.delivery_man_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $deliveryManId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // GET ALL
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                d.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.created_at
            FROM delivery_men d
            INNER JOIN users u
                ON d.user_id = u.user_id
            ORDER BY d.delivery_man_id DESC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // ==========================================
    // UPDATE
    // ==========================================

    public function update(
        int $deliveryManId,
        string $vehicleType,
        string $vehicleNumber
    ): bool {

        $sql = "
            UPDATE delivery_men
            SET
                vehicle_type = ?,
                vehicle_number = ?
            WHERE delivery_man_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssi",
            $vehicleType,
            $vehicleNumber,
            $deliveryManId
        );

        return $stmt->execute();
    }


    // ==========================================
    // COUNT
    // ==========================================

    public function countAll(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM delivery_men
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}