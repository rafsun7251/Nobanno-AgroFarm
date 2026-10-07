<?php

require_once __DIR__ . '/Database.php';

class Customer
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE CUSTOMER
    // ==========================================

    public function create(
        int $userId,
        string $address
    ): bool {

        $sql = "
            INSERT INTO customers
            (
                user_id,
                address
            )
            VALUES (?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "is",
            $userId,
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
                c.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.role
            FROM customers c
            INNER JOIN users u
                ON c.user_id = u.user_id
            WHERE c.user_id = ?
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
    // FIND BY CUSTOMER ID
    // ==========================================

    public function findById(int $customerId): ?array
    {
        $sql = "
            SELECT
                c.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone
            FROM customers c
            INNER JOIN users u
                ON c.user_id = u.user_id
            WHERE c.customer_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("i", $customerId);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // GET ALL CUSTOMERS
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                c.customer_id,
                c.user_id,
                c.address,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.created_at
            FROM customers c
            INNER JOIN users u
                ON c.user_id = u.user_id
            ORDER BY c.customer_id DESC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // ==========================================
    // UPDATE ADDRESS
    // ==========================================

    public function updateAddress(
        int $customerId,
        string $address
    ): bool {

        $sql = "
            UPDATE customers
            SET address = ?
            WHERE customer_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $address,
            $customerId
        );

        return $stmt->execute();
    }


    // ==========================================
    // COUNT CUSTOMERS
    // ==========================================

    public function countAll(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM customers
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}