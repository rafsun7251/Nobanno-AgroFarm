<?php
/*
 * Database checker - open http://localhost/Nobanno-AgroFarm/check_db.php
 * Compares your real tables with what the code expects and prints
 * the exact ALTER statements that are missing.
 * Works on localhost only. DELETE this file when you are done.
 */

if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('localhost only');
}

require_once __DIR__ . '/Model/Database.php';

mysqli_report(MYSQLI_REPORT_OFF);

$db = (new Database())->connect();

$dbName = $db->query('SELECT DATABASE()')->fetch_row()[0];

// table => [column => ALTER definition]
$expected = [
    'users' => [
        'user_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'first_name' => 'VARCHAR(50) NOT NULL',
        'last_name' => 'VARCHAR(50) NOT NULL', 'email' => 'VARCHAR(120) NOT NULL',
        'password' => 'VARCHAR(255) NOT NULL', 'role' => "VARCHAR(20) NOT NULL DEFAULT 'customer'",
        'phone' => 'VARCHAR(20) NOT NULL',
    ],
    'customers' => ['customer_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'user_id' => 'INT NOT NULL', 'address' => 'TEXT NULL'],
    'farmers' => [
        'farmer_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'user_id' => 'INT NOT NULL',
        'farm_name' => 'VARCHAR(100) NOT NULL', 'location' => 'VARCHAR(100) NULL', 'address' => 'TEXT NULL',
    ],
    'delivery_men' => ['delivery_man_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'user_id' => 'INT NOT NULL', 'vehicle_type' => 'VARCHAR(50) NULL', 'vehicle_number' => 'VARCHAR(50) NULL'],
    'crops' => [
        'crop_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'farmer_id' => 'INT NOT NULL', 'crop_name' => 'VARCHAR(100) NOT NULL',
        'category' => 'VARCHAR(50) NOT NULL', 'description' => 'TEXT NULL', 'price_per_kg' => 'DECIMAL(10,2) NOT NULL',
        'quantity' => 'DECIMAL(10,2) NOT NULL DEFAULT 0', 'unit' => "VARCHAR(20) NOT NULL DEFAULT 'kg'",
        'status' => "VARCHAR(20) NOT NULL DEFAULT 'available'", 'image' => 'VARCHAR(255) NULL',
    ],
    'cart' => ['cart_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'customer_id' => 'INT NOT NULL', 'crop_id' => 'INT NOT NULL', 'quantity' => 'INT NOT NULL DEFAULT 1'],
    'orders' => [
        'order_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'customer_id' => 'INT NOT NULL', 'total_amount' => 'DECIMAL(10,2) NOT NULL',
        'delivery_charge' => 'DECIMAL(10,2) NOT NULL DEFAULT 0', 'delivery_address' => 'TEXT NOT NULL',
        'status' => "VARCHAR(20) NOT NULL DEFAULT 'pending'", 'order_date' => 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
    ],
    'order_items' => [
        'order_item_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'order_id' => 'INT NOT NULL', 'crop_id' => 'INT NOT NULL', 'farmer_id' => 'INT NOT NULL',
        'quantity' => 'DECIMAL(10,2) NOT NULL', 'price_per_kg' => 'DECIMAL(10,2) NOT NULL', 'subtotal' => 'DECIMAL(10,2) NOT NULL',
    ],
    'payments' => [
        'payment_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'order_id' => 'INT NOT NULL', 'customer_id' => 'INT NOT NULL',
        'amount' => 'DECIMAL(10,2) NOT NULL', 'payment_method' => 'VARCHAR(30) NOT NULL',
        'transaction_id' => 'VARCHAR(100) NULL', 'payment_status' => "VARCHAR(20) NOT NULL DEFAULT 'pending'",
    ],
    'deliveries' => [
        'delivery_id' => 'INT AUTO_INCREMENT PRIMARY KEY', 'order_id' => 'INT NOT NULL', 'delivery_man_id' => 'INT NULL',
        'delivery_address' => 'TEXT NOT NULL', 'delivery_status' => "VARCHAR(30) NOT NULL DEFAULT 'assigned'",
    ],
];

header('Content-Type: text/plain; charset=utf-8');

echo "Database: $dbName\n\n";

$problems = 0;
$fixes = [];

foreach ($expected as $table => $cols) {

    $res = $db->query(
        "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA
         FROM INFORMATION_SCHEMA.COLUMNS
         WHERE TABLE_SCHEMA = '" . $db->real_escape_string($dbName) . "'
           AND TABLE_NAME = '$table'"
    );

    $actual = [];
    while ($r = $res->fetch_assoc()) {
        $actual[$r['COLUMN_NAME']] = $r;
    }

    if (!$actual) {
        echo "[MISSING TABLE] $table  -> import database/schema.sql\n";
        $problems++;
        continue;
    }

    $lines = [];

    foreach ($cols as $col => $def) {

        if (!isset($actual[$col])) {
            $lines[] = "  missing column: $col";
            if (stripos($def, 'PRIMARY KEY') === false) {
                $fixes[] = "ALTER TABLE `$table` ADD COLUMN `$col` $def;";
            }
            $problems++;
            continue;
        }

        $a = $actual[$col];

        // ENUM columns: show allowed values
        if (stripos($a['COLUMN_TYPE'], 'enum') === 0) {
            $lines[] = "  $col is {$a['COLUMN_TYPE']}  (code writes other values? check)";
        }
    }

    // Columns that exist, are NOT NULL, no default, and code never writes
    foreach ($actual as $col => $a) {
        if (
            !isset($cols[$col]) && $a['IS_NULLABLE'] === 'NO'
            && $a['COLUMN_DEFAULT'] === null && stripos($a['EXTRA'], 'auto_increment') === false
        ) {
            $lines[] = "  extra column `$col` is NOT NULL with no default -> every INSERT will fail";
            $fixes[] = "ALTER TABLE `$table` MODIFY COLUMN `$col` {$a['COLUMN_TYPE']} NULL;";
            $problems++;
        }
    }

    echo ($lines ? "[CHECK]   " : "[ok]      ") . $table . "\n";
    foreach ($lines as $l) echo $l . "\n";
}

// payments: UNIQUE indexes + actual definition
echo "\n--- payments definition ---\n";
$r = $db->query('SHOW CREATE TABLE payments');
if ($r) echo $r->fetch_row()[1] . "\n";

echo "\n--- RESULT ---\n";
echo $problems ? "$problems problem(s) found.\n" : "Columns look fine.\n";

if ($fixes) {
    echo "\nRun these in phpMyAdmin (SQL tab):\n\n" . implode("\n", array_unique($fixes)) . "\n";
}
