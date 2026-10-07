<?php
$pageTitle = 'Inventory';

$extraCss = [
    'public/css/farmer.css'
];

require __DIR__ . '/../layouts/header.php';
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <div>
                <h1>Inventory</h1>
                <p>Current stock of your products.</p>
            </div>

            <a href="index.php?page=farmer-add-crop" class="btn btn-primary">
                + Add Crop
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Crop</th>
                        <th>Category</th>
                        <th>In Stock</th>
                        <th>Price</th>
                        <th>Stock Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($crops)): ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                No crops added yet.
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($crops as $crop): ?>
                            <tr>
                                <td><?= e($crop['crop_name']) ?></td>
                                <td><?= e($crop['category']) ?></td>
                                <td>
                                    <?= number_format((float)$crop['quantity'], 2) ?>
                                    <?= e($crop['unit'] ?? 'kg') ?>
                                    <?php if ((float)$crop['quantity'] <= 0): ?>
                                        <span class="status-badge status-unavailable">
                                            Out of stock
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>৳<?= number_format((float)$crop['price_per_kg'], 2) ?></td>
                                <td>
                                    ৳<?= number_format(
                                        (float)$crop['price_per_kg'] * (float)$crop['quantity'],
                                        2
                                    ) ?>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= e($crop['status']) ?>">
                                        <?= e($crop['status']) ?>
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
