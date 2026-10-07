<?php
$title = 'Orders';

$columns = [
    'Order'    => fn($r) => '#' . (int)$r['order_id'],
    'Customer' => fn($r) => e(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
    'Phone'    => 'phone',
    'Total'    => fn($r) => '৳' . number_format((float)($r['total_amount'] ?? 0), 2),
    'Status'   => fn($r) => '<span class="status-badge status-' . e($r['status']) . '">' . e($r['status']) . '</span>',
    'Date'     => 'order_date',
    ''         => fn($r) => '<a class="btn btn-small" href="index.php?page=admin-order-details&id=' . (int)$r['order_id'] . '">Details</a>',
];

$rows = $orders;

require __DIR__ . '/_list.php';
