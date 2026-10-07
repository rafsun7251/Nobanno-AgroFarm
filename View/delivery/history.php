<?php
$pageTitle = 'Delivery History';

$extraCss = [
    'public/css/delivery.css'
];

require __DIR__ . '/../layouts/header.php';
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <h1>Delivery History</h1>
            <p>Deliveries you have completed.</p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Delivery</th>
                        <th>Order</th>
                        <th>Address</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($deliveries)): ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                No completed deliveries yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($deliveries as $delivery): ?>
                            <tr>
                                <td>#<?= (int)$delivery['delivery_id'] ?></td>
                                <td>#<?= (int)$delivery['order_id'] ?></td>
                                <td><?= e($delivery['delivery_address']) ?></td>
                                <td>
                                    <span class="status-badge status-delivered">
                                        <?= e($delivery['delivery_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
