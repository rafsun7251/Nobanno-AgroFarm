<?php

$pageTitle = 'My Orders';

$extraCss = [
    'public/css/customer.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">

            <h1>
                My Orders
            </h1>

        </div>


        <?php if (empty($orders)): ?>

            <div class="empty-state">

                <h2>
                    No orders yet
                </h2>

                <p>
                    Your placed orders
                    will appear here.
                </p>

                <a
                    href="index.php?page=crops"
                    class="btn btn-primary"
                >
                    Start Shopping
                </a>

            </div>


        <?php else: ?>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Order ID
                            </th>

                            <th>
                                Date
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
                            $orders
                            as $order
                        ): ?>

                            <tr>

                                <td>
                                    #<?= (int)$order[
                                        'order_id'
                                    ] ?>
                                </td>


                                <td>
                                    <?= htmlspecialchars(
                                        $order[
                                            'order_date'
                                        ]
                                        ?? ''
                                    ) ?>
                                </td>


                                <td>

                                    ৳<?= number_format(
                                        (float)$order[
                                            'total_amount'
                                        ],
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="status-badge"
                                    >

                                        <?= htmlspecialchars(
                                            $order[
                                                'status'
                                            ]
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="index.php?page=customer-order-details&id=<?= (int)$order['order_id'] ?>"
                                        class="btn btn-small"
                                    >
                                        Details
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>