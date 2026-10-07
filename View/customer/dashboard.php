<?php

$pageTitle = 'Customer Dashboard';

$extraCss = [
    'public/css/dashboard.css',
    'public/css/customer.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="dashboard-container">


        <div class="dashboard-header">

            <div>

                <h1>
                    Welcome,
                    <?= htmlspecialchars(
                        $customer['first_name']
                        ?? 'Customer'
                    ) ?>
                </h1>

                <p>
                    Discover fresh products
                    from local farmers.
                </p>

            </div>


            <a
                href="index.php?page=crops"
                class="btn btn-primary"
            >
                Browse Crops
            </a>

        </div>


        <div class="stats-grid">

            <div class="stat-card">

                <h3>
                    Cart Items
                </h3>

                <strong>
                    <?= (int)$cartCount ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    My Orders
                </h3>

                <strong>
                    <?= count($orders) ?>
                </strong>

            </div>

        </div>


        <section class="dashboard-section">

            <div class="section-header">

                <h2>
                    Available Products
                </h2>

                <a
                    href="index.php?page=crops"
                >
                    View All
                </a>

            </div>


            <div class="product-grid">

                <?php if (empty($crops)): ?>

                    <p>
                        No products available
                        at the moment.
                    </p>

                <?php else: ?>

                    <?php foreach (
                        array_slice(
                            $crops,
                            0,
                            8
                        )
                        as $crop
                    ): ?>

                        <div class="product-card">

                            <?php if (
                                !empty(
                                    $crop['image']
                                )
                            ): ?>

                                <img
                                    src="<?= htmlspecialchars(
                                        $crop['image']
                                    ) ?>"
                                    alt="<?= htmlspecialchars(
                                        $crop['crop_name']
                                    ) ?>"
                                >

                            <?php endif; ?>


                            <div class="product-content">

                                <h3>
                                    <?= htmlspecialchars(
                                        $crop['crop_name']
                                    ) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars(
                                        $crop['category']
                                    ) ?>
                                </p>

                                <strong>
                                    ৳<?= number_format(
                                        (float)$crop[
                                            'price_per_kg'
                                        ],
                                        2
                                    ) ?>
                                    /kg
                                </strong>


                                <a
                                    href="index.php?page=product-details&id=<?= (int)$crop['crop_id'] ?>"
                                    class="btn btn-secondary"
                                >
                                    View Details
                                </a>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </section>


        <section class="dashboard-section">

            <div class="section-header">

                <h2>
                    Recent Orders
                </h2>

                <a
                    href="index.php?page=customer-orders"
                >
                    View All
                </a>

            </div>


            <?php if (empty($orders)): ?>

                <p>
                    You haven't placed
                    any orders yet.
                </p>

            <?php else: ?>

                <div class="table-container">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Order ID
                                </th>

                                <th>
                                    Total
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach (
                                array_slice(
                                    $orders,
                                    0,
                                    5
                                )
                                as $order
                            ): ?>

                                <tr>

                                    <td>
                                        #<?= (int)$order['order_id'] ?>
                                    </td>

                                    <td>
                                        ৳<?= number_format(
                                            (float)$order['total_amount'],
                                            2
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $order['status']
                                        ) ?>
                                    </td>

                                    <td>

                                        <a
                                            href="index.php?page=customer-order-details&id=<?= (int)$order['order_id'] ?>"
                                        >
                                            View
                                        </a>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </section>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>