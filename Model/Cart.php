<?php

require_once __DIR__ . '/Database.php';

class Cart
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }



    // GET CUSTOMER CART

    public function getByCustomer(int $customerId): array
    {
        $sql = "
            SELECT
                cart.cart_id,
                cart.customer_id,
                cart.crop_id,
                cart.quantity AS cart_quantity,

                crops.crop_name,
                crops.category,
                crops.price_per_kg,
                crops.quantity AS available_quantity,
                crops.unit,
                crops.status,

                farmers.farmer_id,
                farmers.farm_name,
                farmers.location,

                users.first_name AS farmer_first_name,
                users.last_name AS farmer_last_name

            FROM cart

            INNER JOIN crops
                ON cart.crop_id = crops.crop_id

            INNER JOIN farmers
                ON crops.farmer_id = farmers.farmer_id

            INNER JOIN users
                ON farmers.user_id = users.user_id

            WHERE cart.customer_id = ?

            ORDER BY cart.cart_id DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $customerId);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }



    // FIND CART ITEM
    

    public function findItem(
        int $customerId,
        int $cropId
    ): ?array {

        $sql = "
            SELECT *
            FROM cart
            WHERE customer_id = ?
              AND crop_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "ii",
            $customerId,
            $cropId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    
    // ADD TO CART
    

    public function add(
        int $customerId,
        int $cropId,
        int $quantity
    ): bool {

        $existing = $this->findItem(
            $customerId,
            $cropId
        );

        if ($existing) {

            $newQuantity =
                (int)$existing['quantity']
                + $quantity;

            return $this->updateQuantity(
                (int)$existing['cart_id'],
                $newQuantity
            );
        }

        $sql = "
            INSERT INTO cart
            (
                customer_id,
                crop_id,
                quantity
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iii",
            $customerId,
            $cropId,
            $quantity
        );

        return $stmt->execute();
    }


    
    // UPDATE QUANTITY
    

    public function updateQuantity(
        int $cartId,
        int $quantity,
        ?int $customerId = null
    ): bool {

        if ($quantity <= 0) {
            return $this->remove($cartId, $customerId);
        }

        $sql = "
            UPDATE cart
            SET quantity = ?
            WHERE cart_id = ?
              AND (? IS NULL OR customer_id = ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iiii",
            $quantity,
            $cartId,
            $customerId,
            $customerId
        );

        return $stmt->execute();
    }


    
    // REMOVE
    

    public function remove(
        int $cartId,
        ?int $customerId = null
    ): bool {
        $sql = "
            DELETE FROM cart
            WHERE cart_id = ?
              AND (? IS NULL OR customer_id = ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "iii",
            $cartId,
            $customerId,
            $customerId
        );

        return $stmt->execute();
    }


    
    // CLEAR CUSTOMER CART
    

    public function clear(int $customerId): bool
    {
        $sql = "
            DELETE FROM cart
            WHERE customer_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "i",
            $customerId
        );

        return $stmt->execute();
    }


    
    // CART COUNT
    

    public function countItems(int $customerId): int
    {
        $sql = "
            SELECT COALESCE(
                SUM(quantity),
                0
            ) AS total
            FROM cart
            WHERE customer_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param(
            "i",
            $customerId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}