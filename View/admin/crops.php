<?php
$title = 'All Crops';

$columns = [
    'ID'       => 'crop_id',
    'Crop'     => 'crop_name',
    'Category' => 'category',
    'Farm'     => 'farm_name',
    'Price'    => fn($r) => '৳' . number_format((float)($r['price_per_kg'] ?? 0), 2),
    'Stock'    => fn($r) => e(number_format((float)$r['quantity'], 2) . ' ' . ($r['unit'] ?? 'kg')),
    'Status'   => fn($r) => '<span class="status-badge status-' . e($r['status']) . '">' . e($r['status']) . '</span>',
];

$rows = $crops;

require __DIR__ . '/_list.php';
