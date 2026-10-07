<?php

require_once __DIR__ . '/Database.php';

class Payment
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE PAYMENT
    // ==========================================

    public function create(
        int $orderId,
        int $customerId,
        float $amount,
        string $paymentMethod,
        ?string $transactionId = null
    ): int|false {

        $status = "pending";

        // Empty reference (e.g. cash on delivery) is stored as NULL,
        // not "". An empty string breaks a UNIQUE transaction_id
        // column from the second cash order onwards.
        $transactionId = trim((string)$transactionId);

        if ($transactionId === "") {
            $transactionId = null;
        }

        $sql = "
            INSERT INTO payments
            (
                order_id,
                customer_id,
                amount,
                payment_method,
                transaction_id,
                payment_status
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iidsss",
            $orderId,
            $customerId,
            $amount,
            $paymentMethod,
            $transactionId,
            $status
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }

        return false;
    }


    // ==========================================
    // GET PAYMENT BY ORDER
    // ==========================================

    public function findByOrder(
        int $orderId
    ): ?array {

        $sql = "
            SELECT *
            FROM payments
            WHERE order_id = ?
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
    // GET ALL PAYMENTS
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                p.*,

                o.status AS order_status,
                o.order_date,

                c.customer_id,

                u.first_name,
                u.last_name,
                u.email,
                u.phone

            FROM payments p

            INNER JOIN orders o
                ON p.order_id = o.order_id

            INNER JOIN customers c
                ON p.customer_id = c.customer_id

            INNER JOIN users u
                ON c.user_id = u.user_id

            ORDER BY p.payment_id DESC
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
    // UPDATE PAYMENT STATUS
    // ==========================================

    public function updateStatus(
        int $paymentId,
        string $status
    ): bool {

        $sql = "
            UPDATE payments
            SET payment_status = ?
            WHERE payment_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $status,
            $paymentId
        );

        return $stmt->execute();
    }


    // ==========================================
    // TOTAL REVENUE
    // ==========================================

    public function totalRevenue(): float
    {
        $sql = "
            SELECT
                COALESCE(
                    SUM(amount),
                    0
                ) AS total
            FROM payments
            WHERE payment_status = 'paid'
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (float)$row['total'];
    }
}