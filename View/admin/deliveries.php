<?php
$title = 'Deliveries';

$columns = [
    'ID'       => 'delivery_id',
    'Order'    => fn($r) => '#' . (int)$r['order_id'],
    'Customer' => fn($r) => e(($r['customer_first_name'] ?? '') . ' ' . ($r['customer_last_name'] ?? '')),
    'Address'  => 'delivery_address',
    'Status'   => fn($r) => '<span class="status-badge">' . e($r['delivery_status']) . '</span>',
    'Delivery Man' => function ($r) use ($deliveryMen) {

        $html = '<form action="index.php?page=admin-assign-delivery" method="POST" class="inline-form">'
              . '<input type="hidden" name="delivery_id" value="' . (int)$r['delivery_id'] . '">'
              . '<select name="delivery_man_id">';

        foreach ($deliveryMen as $dm) {
            $html .= '<option value="' . (int)$dm['delivery_man_id'] . '"'
                   . ((int)($r['delivery_man_id'] ?? 0) === (int)$dm['delivery_man_id'] ? ' selected' : '')
                   . '>' . e($dm['first_name'] . ' ' . $dm['last_name']) . '</option>';
        }

        return $html . '</select> '
             . '<button type="submit" class="btn btn-small">Assign</button></form>';
    },
];

$rows = $deliveries;

require __DIR__ . '/_list.php';
