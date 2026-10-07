<?php

$pageTitle = 'Pending Deliveries';

$extraCss = [
    'public/css/delivery.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">

            <h1>
                Pending Deliveries
            </h1>

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

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (
                        empty($deliveries)
                    ): ?>

                        <tr>

                            <td
                                colspan="5"
                                class="text-center"
                            >
                                No pending deliveries.
                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach (
                            $deliveries
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

                                <td>

                                    <a
                                        href="index.php?page=delivery-edit&id=<?= (int)$delivery['delivery_id'] ?>"
                                        class="btn btn-small"
                                    >
                                        Update
                                    </a>

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