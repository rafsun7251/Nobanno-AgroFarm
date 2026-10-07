<?php

$pageTitle = 'Farmer Dashboard';

$extraCss = [
    'public/css/dashboard.css',
    'public/css/farmer.css'
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
                        $farmer['farm_name']
                        ?? 'Farmer'
                    ) ?>

                </h1>

                <p>
                    Manage your farm
                    products and inventory.
                </p>

            </div>


            <a
                href="index.php?page=farmer-add-crop"
                class="btn btn-primary"
            >
                Add New Crop
            </a>

        </div>


        <div class="stats-grid">


            <div class="stat-card">

                <h3>
                    Total Crops
                </h3>

                <strong>
                    <?= (int)$totalCrops ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    Available Crops
                </h3>

                <strong>
                    <?= (int)$activeCrops ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    Unavailable
                </h3>

                <strong>
                    <?= (int)$unavailableCrops ?>
                </strong>

            </div>


            <div class="stat-card">

                <h3>
                    Total Quantity
                </h3>

                <strong>
                    <?= number_format(
                        $totalQuantity,
                        2
                    ) ?>
                </strong>

            </div>

        </div>


        <section class="dashboard-section">

            <div class="section-header">

                <h2>
                    My Products
                </h2>

                <a
                    href="index.php?page=farmer-crops"
                >
                    Manage All
                </a>

            </div>


            <div class="table-container">

                <table>

                    <thead>

                        <tr>

                            <th>
                                Crop
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach (
                            array_slice(
                                $crops,
                                0,
                                10
                            )
                            as $crop
                        ): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars(
                                        $crop[
                                            'crop_name'
                                        ]
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $crop[
                                            'category'
                                        ]
                                    ) ?>
                                </td>

                                <td>

                                    ৳<?= number_format(
                                        (float)$crop[
                                            'price_per_kg'
                                        ],
                                        2
                                    ) ?>

                                </td>

                                <td>

                                    <?= number_format(
                                        (float)$crop[
                                            'quantity'
                                        ],
                                        2
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $crop[
                                            'unit'
                                        ]
                                        ?? 'kg'
                                    ) ?>

                                </td>

                                <td>

                                    <?= htmlspecialchars(
                                        $crop[
                                            'status'
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