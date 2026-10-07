<?php
$title = 'Customers';

$columns = [
    'ID'      => 'customer_id',
    'Name'    => fn($r) => e(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
    'Email'   => 'email',
    'Phone'   => 'phone',
    'Address' => 'address',
];

$rows = $customers;

require __DIR__ . '/_list.php';
