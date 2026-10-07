<?php
$title = 'Delivery Men';

$columns = [
    'ID'             => 'delivery_man_id',
    'Name'           => fn($r) => e(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
    'Email'          => 'email',
    'Phone'          => 'phone',
    'Vehicle'        => 'vehicle_type',
    'Vehicle Number' => 'vehicle_number',
];

$rows = $deliveryMen;

require __DIR__ . '/_list.php';
