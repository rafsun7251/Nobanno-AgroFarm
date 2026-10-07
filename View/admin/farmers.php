<?php
$title = 'Farmers';

$columns = [
    'ID'        => 'farmer_id',
    'Name'      => fn($r) => e(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
    'Farm'      => 'farm_name',
    'Location'  => 'location',
    'Email'     => 'email',
    'Phone'     => 'phone',
];

$rows = $farmers;

require __DIR__ . '/_list.php';
