<?php

$pageTitle = 'My Orders';

$extraCss = [
    'public/css/farmer.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">

            <div>

                <h1>
                    My Orders
                </h1>

                <p>
                    Orders containing your farm products.
                </p>

            </div>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Order ID
                        </th>

                        <th>
                            Customer
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            My Items Total
                        </th>

                        <th>
                            Delivery Address
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (empty($orders)): ?>

                        <tr>

                            <td
                                colspan="7"
                                class="text-center"
                            >

                                No orders found.

                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach ($orders as $order): ?>

                            <tr>

                                <td>

                                    #<?= (int)$order['order_id'] ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        ($order['first_name'] ?? '') .
                                        ' ' .
                                        ($order['last_name'] ?? '')
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $order['phone'] ?? ''
                                    ) ?>

                                </td>


                                <td>

                                    ৳<?= number_format(
                                        (float)$order['farmer_total'],
                                        2
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $order['delivery_address'] ?? ''
                                    ) ?>

                                </td>


                                <td>

                                    <span class="status-badge">

                                        <?= htmlspecialchars(
                                            $order['status']
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $order['order_date']
                                    ) ?>

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