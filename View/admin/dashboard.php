<?php

$pageTitle = 'Admin Dashboard';

$extraCss = [
    'public/css/dashboard.css',
    'public/css/admin.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="dashboard-container">


        <div class="dashboard-header">

            <div>

                <h1>
                    Admin Dashboard
                </h1>

                <p>
                    Manage Nobanno Agro Farm.
                </p>

            </div>

        </div>


        <div class="stats-grid">


            <div class="stat-card">

                <h3>
                    Users
                </h3>

                <strong>
                    <?= (int)$totalUsers ?>
                </strong>

                <a
                    href="index.php?page=admin-users"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Farmers
                </h3>

                <strong>
                    <?= (int)$totalFarmers ?>
                </strong>

                <a
                    href="index.php?page=admin-farmers"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Customers
                </h3>

                <strong>
                    <?= (int)$totalCustomers ?>
                </strong>

                <a
                    href="index.php?page=admin-customers"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Delivery Men
                </h3>

                <strong>
                    <?= (int)$totalDeliveryMen ?>
                </strong>

                <a
                    href="index.php?page=admin-delivery-men"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Crops
                </h3>

                <strong>
                    <?= (int)$totalCrops ?>
                </strong>

                <a
                    href="index.php?page=admin-crops"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Orders
                </h3>

                <strong>
                    <?= (int)$totalOrders ?>
                </strong>

                <a
                    href="index.php?page=admin-orders"
                >
                    Manage
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Revenue
                </h3>

                <strong>
                    ৳<?= number_format(
                        (float)$totalRevenue,
                        2
                    ) ?>
                </strong>

                <a
                    href="index.php?page=admin-payments"
                >
                    Payments
                </a>

            </div>


            <div class="stat-card">

                <h3>
                    Deliveries
                </h3>

                <strong>
                    <?= (int)$totalDeliveries ?>
                </strong>

                <a
                    href="index.php?page=admin-deliveries"
                >
                    Manage
                </a>

            </div>

        </div>


        <section class="quick-actions">

            <h2>
                Quick Actions
            </h2>


            <div class="action-grid">

                <a
                    href="index.php?page=admin-users"
                    class="action-card"
                >
                    Manage Users
                </a>

                <a
                    href="index.php?page=admin-orders"
                    class="action-card"
                >
                    View Orders
                </a>

                <a
                    href="index.php?page=admin-payments"
                    class="action-card"
                >
                    Payments
                </a>

                <a
                    href="index.php?page=admin-deliveries"
                    class="action-card"
                >
                    Deliveries
                </a>

            </div>

        </section>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>