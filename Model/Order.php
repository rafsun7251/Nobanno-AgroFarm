<?php

require_once __DIR__ . '/Database.php';

class Order
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE ORDER
    // ==========================================

    public function create(
        int $customerId,
        float $totalAmount,
        float $deliveryCharge,
        string $deliveryAddress
    ): int|false {

        $sql = "
            INSERT INTO orders
            (
                customer_id,
                total_amount,
                delivery_charge,
                delivery_address,
                status
            )
            VALUES (?, ?, ?, ?, 'pending')
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "idds",
            $customerId,
            $totalAmount,
            $deliveryCharge,
            $deliveryAddress
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }

        return false;
    }


    // ==========================================
    // FIND ORDER
    // ==========================================

    public function findById(int $orderId): ?array
    {
        $sql = "
            SELECT
                o.*,

                c.customer_id,

                u.first_name,
                u.last_name,
                u.email,
                u.phone

            FROM orders o

            INNER JOIN customers c
                ON o.customer_id = c.customer_id

            INNER JOIN users u
                ON c.user_id = u.user_id

            WHERE o.order_id = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "i",
            $orderId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // CUSTOMER ORDERS
    // ==========================================

    public function getByCustomer(
        int $customerId
    ): array {

        $sql = "
            SELECT
                o.*
            FROM orders o
            WHERE o.customer_id = ?
            ORDER BY o.order_date DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param(
            "i",
            $customerId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }

    // ==========================================
// FARMER ORDERS
// ==========================================

    public function getByFarmer(
        int $farmerId
    ): array {

        // farmer_total = only this farmer's items in the order
        // (total_amount is the whole order, incl. other farmers).

        $sql = "
            SELECT
                o.order_id,
                o.customer_id,
                o.total_amount,
                o.delivery_charge,
                o.delivery_address,
                o.status,
                o.order_date,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                SUM(oi.subtotal) AS farmer_total

            FROM orders o

            INNER JOIN order_items oi
                ON o.order_id = oi.order_id

            INNER JOIN crops c
                ON oi.crop_id = c.crop_id

            INNER JOIN customers cu
                ON o.customer_id = cu.customer_id

            INNER JOIN users u
                ON cu.user_id = u.user_id

            WHERE c.farmer_id = ?

            GROUP BY
                o.order_id,
                o.customer_id,
                o.total_amount,
                o.delivery_charge,
                o.delivery_address,
                o.status,
                o.order_date,
                u.first_name,
                u.last_name,
                u.email,
                u.phone

            ORDER BY o.order_date DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $farmerId);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    public function getAll(): array
    {
        $sql = "
            SELECT
                o.*,

                c.customer_id,

                u.first_name,
                u.last_name,
                u.email,
                u.phone

            FROM orders o

            INNER JOIN customers c
                ON o.customer_id = c.customer_id

            INNER JOIN users u
                ON c.user_id = u.user_id

            ORDER BY o.order_date DESC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    // ==========================================
    // UPDATE STATUS
    // ==========================================

    public function updateStatus(
        int $orderId,
        string $status
    ): bool {

        $sql = "
            UPDATE orders
            SET status = ?
            WHERE order_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $status,
            $orderId
        );

        return $stmt->execute();
    }


    // ==========================================
    // COUNT ORDERS
    // ==========================================

    public function countAll(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM orders
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}