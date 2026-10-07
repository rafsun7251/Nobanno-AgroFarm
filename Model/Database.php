<?php

class Database
{
    private string $host = "localhost";
    private string $username = "root";
    private string $password = "";
    private string $database = "NobannoAgro";

    // One connection shared by every model in the request.
    // (Before: every model opened its own connection, and
    //  transactions could not span models.)
    private static ?mysqli $connection = null;

    public function connect(): mysqli
    {
        if (self::$connection === null) {

            $connection = new mysqli(
                getenv('DB_HOST') ?: $this->host,
                getenv('DB_USER') ?: $this->username,
                getenv('DB_PASS') !== false
                    ? getenv('DB_PASS')
                    : $this->password,
                getenv('DB_NAME') ?: $this->database
            );

            if ($connection->connect_error) {
                die(
                    "Database connection failed: " .
                    $connection->connect_error
                );
            }

            $connection->set_charset("utf8mb4");

            self::$connection = $connection;
        }

        return self::$connection;
    }
}
