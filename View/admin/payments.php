<?php
$title = 'Payments';

$columns = [
    'ID'          => 'payment_id',
    'Order'       => fn($r) => '#' . (int)$r['order_id'],
    'Customer'    => fn($r) => e(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
    'Amount'      => fn($r) => '৳' . number_format((float)$r['amount'], 2),
    'Method'      => 'payment_method',
    'Transaction' => 'transaction_id',
    'Status'      => function ($r) {

        $html = '<form action="index.php?page=admin-payment-update" method="POST" class="inline-form">'
              . '<input type="hidden" name="payment_id" value="' . (int)$r['payment_id'] . '">'
              . '<select name="payment_status">';

        foreach (['pending', 'paid', 'failed'] as $st) {
            $html .= '<option value="' . $st . '"'
                   . ($r['payment_status'] === $st ? ' selected' : '')
                   . '>' . ucfirst($st) . '</option>';
        }

        return $html . '</select> '
             . '<button type="submit" class="btn btn-small">Save</button></form>';
    },
];

$rows = $payments;

require __DIR__ . '/_list.php';
