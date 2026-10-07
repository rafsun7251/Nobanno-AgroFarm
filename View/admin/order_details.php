<?php
$pageTitle = 'Order #' . (int)($order['order_id'] ?? 0);

$extraCss = [
    'public/css/admin.css'
];

require __DIR__ . '/../layouts/header.php';
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <div>
                <h1>Order #<?= (int)$order['order_id'] ?></h1>
                <p>
                    <?= e($order['first_name'] . ' ' . $order['last_name']) ?>
                    &middot; <?= e($order['phone']) ?>
                    &middot; <?= e($order['order_date']) ?>
                </p>
            </div>

            <a href="index.php?page=admin-orders" class="btn btn-secondary">
                Back to Orders
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Farm</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= e($item['crop_name']) ?></td>
                            <td><?= e($item['farm_name']) ?></td>
                            <td>
                                <?= number_format((float)$item['quantity'], 2) ?>
                                <?= e($item['unit'] ?? 'kg') ?>
                            </td>
                            <td>৳<?= number_format((float)$item['price_per_kg'], 2) ?></td>
                            <td>৳<?= number_format((float)$item['subtotal'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <dl class="detail-list">
            <dt>Status</dt>
            <dd><?= e($order['status']) ?></dd>

            <dt>Delivery address</dt>
            <dd><?= e($order['delivery_address']) ?></dd>

            <dt>Payment</dt>
            <dd>
                <?php if (!empty($payment)): ?>
                    <?= e($payment['payment_method']) ?> /
                    <?= e($payment['payment_status']) ?>
                <?php else: ?>
                    No payment record
                <?php endif; ?>
            </dd>

            <dt>Total</dt>
            <dd><strong>৳<?= number_format((float)$order['total_amount'], 2) ?></strong></dd>
        </dl>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
