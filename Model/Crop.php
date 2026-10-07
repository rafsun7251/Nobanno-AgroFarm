<?php

require_once __DIR__ . '/Database.php';

class Crop
{
    private mysqli $db;


    public function __construct()
    {
        $database = new Database();

        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE CROP
    // ==========================================

    public function create(
        int $farmerId,
        string $cropName,
        string $category,
        string $description,
        float $pricePerKg,
        float $quantity,
        string $unit,
        string $status = "available"
    ): int|false {

        $sql = "
            INSERT INTO crops
            (
                farmer_id,
                crop_name,
                category,
                description,
                price_per_kg,
                quantity,
                unit,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "isssddss",
            $farmerId,
            $cropName,
            $category,
            $description,
            $pricePerKg,
            $quantity,
            $unit,
            $status
        );

        if ($stmt->execute()) {

            return $stmt->insert_id;
        }

        return false;
    }


    // ==========================================
    // GET CROP BY ID
    // ==========================================

    public function findById(
        int $cropId
    ): ?array {

        $sql = "
            SELECT
                c.*,
                f.farm_name,
                f.location,
                u.first_name,
                u.last_name
            FROM crops c

            INNER JOIN farmers f
                ON c.farmer_id = f.farmer_id

            INNER JOIN users u
                ON f.user_id = u.user_id

            WHERE c.crop_id = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param(
            "i",
            $cropId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        if (
            $result &&
            $result->num_rows > 0
        ) {

            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // GET FARMER CROPS
    // ==========================================

    public function getByFarmer(
        int $farmerId
    ): array {

        $sql = "
            SELECT
                crop_id,
                farmer_id,
                crop_name,
                category,
                description,
                price_per_kg,
                quantity,
                unit,
                status,
                created_at

            FROM crops

            WHERE farmer_id = ?

            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param(
            "i",
            $farmerId
        );

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    // ==========================================
    // GET ALL CROPS
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                c.*,
                f.farm_name,
                f.location,
                u.first_name,
                u.last_name

            FROM crops c

            INNER JOIN farmers f
                ON c.farmer_id = f.farmer_id

            INNER JOIN users u
                ON f.user_id = u.user_id

            ORDER BY c.created_at DESC
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
    // GET AVAILABLE CROPS
    // ==========================================

    public function getActive(): array
    {
        $sql = "
            SELECT
                c.*,
                f.farm_name,
                f.location,
                u.first_name,
                u.last_name

            FROM crops c

            INNER JOIN farmers f
                ON c.farmer_id = f.farmer_id

            INNER JOIN users u
                ON f.user_id = u.user_id

            WHERE c.status = 'available'

            AND c.quantity > 0

            ORDER BY c.created_at DESC
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
    // SEARCH CROPS
    // ==========================================

    public function search(
        string $keyword = "",
        string $category = ""
    ): array {

        $sql = "
            SELECT
                c.*,
                f.farm_name,
                f.location,
                u.first_name,
                u.last_name

            FROM crops c

            INNER JOIN farmers f
                ON c.farmer_id = f.farmer_id

            INNER JOIN users u
                ON f.user_id = u.user_id

            WHERE c.status = 'available'

            AND c.quantity > 0
        ";

        $params = [];

        $types = "";


        // SEARCH

        if ($keyword !== "") {

            $sql .= "
                AND (
                    LOWER(c.crop_name)
                        LIKE LOWER(?)

                    OR LOWER(c.description)
                        LIKE LOWER(?)

                    OR LOWER(c.category)
                        LIKE LOWER(?)
                )
            ";

            $search =
                "%" . $keyword . "%";


            $params[] = $search;

            $params[] = $search;

            $params[] = $search;

            $types .= "sss";
        }


        // CATEGORY

        if ($category !== "") {

            $sql .= "
                AND LOWER(c.category)
                    = LOWER(?)
            ";

            $params[] = $category;

            $types .= "s";
        }


        $sql .= "
            ORDER BY c.created_at DESC
        ";


        $stmt =
            $this->db->prepare($sql);


        if (!$stmt) {
            return [];
        }


        if (!empty($params)) {

            $stmt->bind_param(
                $types,
                ...$params
            );
        }


        $stmt->execute();


        $result =
            $stmt->get_result();


        return $result->fetch_all(
            MYSQLI_ASSOC
        );
    }


    // ==========================================
    // UPDATE CROP
    // ==========================================

    public function update(
        int $cropId,
        string $cropName,
        string $category,
        string $description,
        float $pricePerKg,
        float $quantity,
        string $unit,
        string $status
    ): bool {

        $sql = "
            UPDATE crops

            SET
                crop_name = ?,
                category = ?,
                description = ?,
                price_per_kg = ?,
                quantity = ?,
                unit = ?,
                status = ?

            WHERE crop_id = ?
        ";

        $stmt =
            $this->db->prepare($sql);


        if (!$stmt) {
            return false;
        }


        $stmt->bind_param(
            "sssddssi",
            $cropName,
            $category,
            $description,
            $pricePerKg,
            $quantity,
            $unit,
            $status,
            $cropId
        );


        return $stmt->execute();
    }


    // ==========================================
    // UPDATE QUANTITY
    // ==========================================

    public function updateQuantity(
        int $cropId,
        float $quantity
    ): bool {

        $sql = "
            UPDATE crops

            SET quantity = ?

            WHERE crop_id = ?
        ";

        $stmt =
            $this->db->prepare($sql);


        if (!$stmt) {
            return false;
        }


        $stmt->bind_param(
            "di",
            $quantity,
            $cropId
        );


        return $stmt->execute();
    }


    // ==========================================
    // DECREASE QUANTITY (atomic, never goes below zero)
    // ==========================================

    public function decreaseQuantity(
        int $cropId,
        float $amount
    ): bool {

        $sql = "
            UPDATE crops

            SET quantity = quantity - ?

            WHERE crop_id = ?

            AND quantity >= ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "did",
            $amount,
            $cropId,
            $amount
        );

        return $stmt->execute()
            && $stmt->affected_rows === 1;
    }


    // ==========================================
    // DELETE
    // ==========================================

    public function delete(
        int $cropId
    ): bool {

        $sql = "
            DELETE FROM crops

            WHERE crop_id = ?
        ";

        $stmt =
            $this->db->prepare($sql);


        if (!$stmt) {
            return false;
        }


        $stmt->bind_param(
            "i",
            $cropId
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

            FROM crops
        ";

        $result =
            $this->db->query($sql);


        if (!$result) {
            return 0;
        }


        $row =
            $result->fetch_assoc();


        return (int)$row['total'];
    }
}