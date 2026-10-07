<?php

$pageTitle = 'Fresh Crops';

$extraCss = [
    'public/css/crops.css'
];

require __DIR__ . '/../layouts/header.php';

$keyword =
    $keyword ?? '';

$category =
    $category ?? '';

?>

<main class="crops-page">

    <div class="page-container">

        <div class="page-header">

            <h1>
                Fresh Farm Products
            </h1>

            <p>
                Buy fresh products directly
                from local farmers.
            </p>

        </div>


        <form
            method="GET"
            action="index.php"
            class="search-form"
        >

            <input
                type="hidden"
                name="page"
                value="crop-search"
            >


            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars(
                    $keyword
                ) ?>"
                placeholder="Search crops..."
            >


            <select
                name="category"
            >
                <option value="">
                    All Categories
                </option>

                <?php foreach (
                    FarmerController::CATEGORIES
                    as $value => $label
                ): ?>

                    <option
                        value="<?= e($value) ?>"
                        <?= $category === $value ? 'selected' : '' ?>
                    >
                        <?= e($label) ?>
                    </option>

                <?php endforeach; ?>
            </select>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Search
            </button>

        </form>


        <div class="product-grid">

            <?php if (empty($crops)): ?>

                <div class="empty-state">

                    <h3>
                        No products found
                    </h3>

                    <p>
                        Try another search.
                    </p>

                </div>

            <?php else: ?>

                <?php foreach ($crops as $crop): ?>

                    <div class="product-card">

                        <div class="product-image">

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

                            <?php else: ?>

                                <div
                                    class="image-placeholder"
                                >
                                    No Image
                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="product-content">

                            <span class="product-category">
                                <?= htmlspecialchars(
                                    $crop['category']
                                ) ?>
                            </span>


                            <h3>
                                <?= htmlspecialchars(
                                    $crop['crop_name']
                                ) ?>
                            </h3>


                            <p>
                                <?= htmlspecialchars(
                                    $crop['description']
                                    ?? ''
                                ) ?>
                            </p>


                            <div
                                class="product-price"
                            >
                                ৳<?= number_format(
                                    (float)$crop[
                                        'price_per_kg'
                                    ],
                                    2
                                ) ?>

                                <span>
                                    /kg
                                </span>
                            </div>


                            <a
                                href="index.php?page=product-details&id=<?= (int)$crop['crop_id'] ?>"
                                class="btn btn-primary"
                            >
                                View Product
                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>