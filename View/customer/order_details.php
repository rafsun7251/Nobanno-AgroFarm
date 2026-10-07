<?php
$pageTitle = 'Order #' . (int)($order['order_id'] ?? 0);

$extraCss = [
    'public/css/customer.css'
];

require __DIR__ . '/../layouts/header.php';

$itemsTotal = 0.0;
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <div>
                <h1>Order #<?= (int)$order['order_id'] ?></h1>
                <p>
                    Placed on <?= e($order['order_date']) ?> &middot;
                    <span class="status-badge status-<?= e($order['status']) ?>">
                        <?= e($order['status']) ?>
                    </span>
                </p>
            </div>

            <a href="index.php?page=customer-orders" class="btn btn-secondary">
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
                        <?php $itemsTotal += (float)$item['subtotal']; ?>
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
            <dt>Items total</dt>
            <dd>৳<?= number_format($itemsTotal, 2) ?></dd>

            <dt>Delivery charge</dt>
            <dd>৳<?= number_format((float)$order['delivery_charge'], 2) ?></dd>

            <dt>Grand total</dt>
            <dd><strong>৳<?= number_format((float)$order['total_amount'], 2) ?></strong></dd>

            <dt>Delivery address</dt>
            <dd><?= e($order['delivery_address']) ?></dd>
        </dl>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
