<?php

$pageTitle = 'My Crops';

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
                    My Crops
                </h1>

                <p>
                    Manage all your products.
                </p>

            </div>


            <a
                href="index.php?page=farmer-add-crop"
                class="btn btn-primary"
            >
                + Add Crop
            </a>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            Name
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

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (
                        empty($crops)
                    ): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="text-center"
                            >
                                No crops added yet.
                            </td>

                        </tr>


                    <?php else: ?>


                        <?php foreach (
                            $crops
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

                                    <span
                                        class="status-badge status-<?= e($crop['status']) ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $crop[
                                                'status'
                                            ]
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <a
                                        href="index.php?page=farmer-edit-crop&id=<?= (int)$crop['crop_id'] ?>"
                                        class="btn btn-small"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        action="index.php?page=farmer-delete-crop"
                                        method="POST"
                                        class="inline-form"
                                        onsubmit="return confirm('Delete this crop?')"
                                    >
                                        <input
                                            type="hidden"
                                            name="crop_id"
                                            value="<?= (int)$crop['crop_id'] ?>"
                                        >
                                        <button
                                            type="submit"
                                            class="btn btn-small btn-danger"
                                        >
                                            Delete
                                        </button>
                                    </form>

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