<?php

require_once __DIR__ . '/Database.php';

class User
{
    private mysqli $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }


    // ==========================================
    // CREATE USER
    // ==========================================

    public function create(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        string $role,
        string $phone
    ): int|false {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            INSERT INTO users
            (
                first_name,
                last_name,
                email,
                password,
                role,
                phone
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "ssssss",
            $firstName,
            $lastName,
            $email,
            $hashedPassword,
            $role,
            $phone
        );

        if ($stmt->execute()) {
            return $stmt->insert_id;
        }

        return false;
    }


    // ==========================================
    // FIND USER BY EMAIL
    // ==========================================

    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return null;
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }


    // ==========================================
    // FIND USER BY ID
    // ==========================================

    public function findById(int $userId): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE user_id = ?
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
    // LOGIN
    // ==========================================

    public function authenticate(
        string $email,
        string $password
    ): ?array {

        $user = $this->findByEmail($email);

        if (!$user) {
            return null;
        }

        if (
            !password_verify(
                $password,
                $user['password']
            )
        ) {
            return null;
        }

        return $user;
    }


    // ==========================================
    // CHECK EMAIL
    // ==========================================

    public function emailExists(string $email): bool
    {
        $sql = "
            SELECT user_id
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result && $result->num_rows > 0;
    }


    // ==========================================
    // GET ALL USERS
    // ==========================================

    public function getAll(): array
    {
        $sql = "
            SELECT
                user_id,
                first_name,
                last_name,
                email,
                role,
                phone,
                created_at
            FROM users
            ORDER BY created_at DESC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // ==========================================
    // GET USERS BY ROLE
    // ==========================================

    public function getByRole(string $role): array
    {
        $sql = "
            SELECT
                user_id,
                first_name,
                last_name,
                email,
                role,
                phone,
                created_at
            FROM users
            WHERE role = ?
            ORDER BY created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("s", $role);

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }


    // ==========================================
    // UPDATE USER
    // ==========================================

    public function update(
        int $userId,
        string $firstName,
        string $lastName,
        string $email,
        string $role,
        string $phone
    ): bool {

        $sql = "
            UPDATE users
            SET
                first_name = ?,
                last_name = ?,
                email = ?,
                role = ?,
                phone = ?
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "sssssi",
            $firstName,
            $lastName,
            $email,
            $role,
            $phone,
            $userId
        );

        return $stmt->execute();
    }


    // ==========================================
    // UPDATE PASSWORD
    // ==========================================

    public function updatePassword(
        int $userId,
        string $password
    ): bool {

        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $sql = "
            UPDATE users
            SET password = ?
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param(
            "si",
            $hashedPassword,
            $userId
        );

        return $stmt->execute();
    }


    // ==========================================
    // DELETE USER
    // ==========================================

    public function delete(int $userId): bool
    {
        $sql = "
            DELETE FROM users
            WHERE user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $userId);

        return $stmt->execute();
    }


    // ==========================================
    // COUNT USERS
    // ==========================================

    public function countAll(): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM users
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return 0;
        }

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }


    // ==========================================
    // COUNT BY ROLE
    // ==========================================

    public function countByRole(string $role): int
    {
        $sql = "
            SELECT COUNT(*) AS total
            FROM users
            WHERE role = ?
        ";

        $stmt = $this->db->prepare($sql);

        if (!$stmt) {
            return 0;
        }

        $stmt->bind_param("s", $role);

        $stmt->execute();

        $result = $stmt->get_result();

        $row = $result->fetch_assoc();

        return (int)$row['total'];
    }
}