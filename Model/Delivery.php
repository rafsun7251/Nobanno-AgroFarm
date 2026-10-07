<?php

require_once __DIR__ . '/Database.php';

class Delivery
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE DELIVERY
    // ==========================================

    public function create(
        int $orderId,
        int $deliveryManId,
        string $deliveryAddress
    ): int|false {

        $status = "assigned";

        $sql = "
            INSERT INTO deliveries
            (
                order_id,
                delivery_man_id,
                delivery_address,
                delivery_status
            )
            VALUES (?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iiss",
            $orderId,
            $deliveryManId,
            $deliveryAddress,
            $status
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }

        return false;
    }


    // ==========================================
    // FIND DELIVERY
    // ==========================================

    public function findById(
        int $deliveryId
    ): ?array {

        $sql = "
            SELECT
                d.*,

                o.order_id,
                o.total_amount,
                o.order_date,
                o.status AS order_status,

                c.customer_id,

                customer_user.first_name
                    AS customer_first_name,

                customer_user.last_name
                    AS customer_last_name,

                customer_user.phone
                    AS customer_phone,

                dm.delivery_man_id,

                delivery_user.first_name
                    AS delivery_first_name,

                delivery_user.last_name
                    AS delivery_last_name,

                delivery_user.phone
                    AS delivery_phone,

                dm.vehicle_type,
                dm.vehicle_number

            FROM deliveries d

            INNER JOIN orders o
                ON d.order_id = o.order_id

            INNER JOIN customers c
                ON o.customer_id = c.customer_id

            INNER JOIN users customer_user
                ON c.user_id = customer_user.user_id

            LEFT JOIN delivery_men dm
                ON d.delivery_man_id = dm.delivery_man_id

            LEFT JOIN users delivery_user
                ON dm.user_id = delivery_user.user_id

            WHERE d.delivery_id = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "i",
            $deliveryId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // GET ALL DELIVERIES
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                d.*,

                o.order_id,
                o.total_amount,
                o.order_date,
                o.status AS order_status,

                c.customer_id,

                customer_user.first_name
                    AS customer_first_name,

                customer_user.last_name
                    AS customer_last_name,

                customer_user.phone
                    AS customer_phone,

                dm.delivery_man_id,

                delivery_user.first_name
                    AS delivery_first_name,

                delivery_user.last_name
                    AS delivery_last_name,

                delivery_user.phone
                    AS delivery_phone,

                dm.vehicle_type,
                dm.vehicle_number

            FROM deliveries d

            INNER JOIN orders o
                ON d.order_id = o.order_id

            INNER JOIN customers c
                ON o.customer_id = c.customer_id

            INNER JOIN users customer_user
                ON c.user_id = customer_user.user_id

            LEFT JOIN delivery_men dm
                ON d.delivery_man_id = dm.delivery_man_id

            LEFT JOIN users delivery_user
                ON dm.user_id = delivery_user.user_id

            ORDER BY d.delivery_id DESC
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
    // GET DELIVERY MAN DELIVERIES
    // ==========================================

    public function getByDeliveryMan(
        int $deliveryManId
    ): array {

        $sql = "
            SELECT
                d.*,

                o.order_id,
                o.total_amount,
                o.order_date,
                o.status AS order_status,

                c.customer_id,

                customer_user.first_name
                    AS customer_first_name,

                customer_user.last_name
                    AS customer_last_name,

                customer_user.phone
                    AS customer_phone

            FROM deliveries d

            INNER JOIN orders o
                ON d.order_id = o.order_id

            INNER JOIN customers c
                ON o.customer_id = c.customer_id

            INNER JOIN users customer_user
                ON c.user_id = customer_user.user_id

            WHERE d.delivery_man_id = ?

            ORDER BY d.delivery_id DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param(
            "i",
            $deliveryManId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    // ==========================================
    // UPDATE DELIVERY STATUS
    // ==========================================

    public function updateStatus(
        int $deliveryId,
        string $status
    ): bool {

        $sql = "
            UPDATE deliveries
            SET delivery_status = ?
            WHERE delivery_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $status,
            $deliveryId
        );

        return $stmt->execute();
    }


    // ==========================================
    // ASSIGN DELIVERY MAN
    // ==========================================

    public function assign(
        int $deliveryId,
        int $deliveryManId
    ): bool {

        $sql = "
            UPDATE deliveries
            SET
                delivery_man_id = ?,
                delivery_status = 'assigned'
            WHERE delivery_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ii",
            $deliveryManId,
            $deliveryId
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
            FROM deliveries
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}