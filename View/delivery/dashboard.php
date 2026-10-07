<?php

$pageTitle = 'Delivery Dashboard';

$extraCss = [
    'public/css/dashboard.css',
    'public/css/delivery.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="dashboard-container">


        <div class="dashboard-header">

            <div>

                <h1>
                    Delivery Dashboard
                </h1>

                <p>

                    Welcome,

                    <?= htmlspecialchars(
                        $deliveryMan[
                            'first_name'
                        ]
                        ??
                        'Delivery Partner'
                    ) ?>

                </p>

            </div>

        </div>


        <div class="stats-grid">


            <div class="stat-card">

                <h3>
                    Total Deliveries
                </h3>

                <strong>
                    <?= (int)$total ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    Pending
                </h3>

                <strong>
                    <?= (int)$pending ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    Completed
                </h3>

                <strong>
                    <?= (int)$completed ?>
                </strong>

            </div>

        </div>


        <section class="dashboard-section">

            <div class="section-header">

                <h2>
                    Recent Deliveries
                </h2>

                <a
                    href="index.php?page=delivery-pending"
                >
                    View Pending
                </a>

            </div>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Delivery ID
                            </th>

                            <th>
                                Order ID
                            </th>

                            <th>
                                Address
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            array_slice(
                                $deliveries,
                                0,
                                10
                            )
                            as $delivery
                        ): ?>

                            <tr>

                                <td>
                                    #<?= (int)$delivery[
                                        'delivery_id'
                                    ] ?>
                                </td>

                                <td>
                                    #<?= (int)$delivery[
                                        'order_id'
                                    ] ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $delivery[
                                            'delivery_address'
                                        ]
                                        ?? ''
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $delivery[
                                            'delivery_status'
                                        ]
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>