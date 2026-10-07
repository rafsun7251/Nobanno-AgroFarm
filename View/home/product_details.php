<?php

$pageTitle =
    $crop['crop_name']
    ?? 'Product Details';

$extraCss = [
    'public/css/crops.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="product-details-page">

    <div class="page-container">

        <div class="product-details">


            <div class="product-details-image">

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

                    <div class="image-placeholder">
                        No Image
                    </div>

                <?php endif; ?>

            </div>


            <div class="product-details-content">

                <span class="product-category">

                    <?= htmlspecialchars(
                        $crop['category']
                    ) ?>

                </span>


                <h1>

                    <?= htmlspecialchars(
                        $crop['crop_name']
                    ) ?>

                </h1>


                <p class="product-description">

                    <?= htmlspecialchars(
                        $crop['description']
                        ?? ''
                    ) ?>

                </p>


                <div class="product-price-large">

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


                <p>

                    Available:

                    <strong>
                        <?= number_format(
                            (float)$crop['quantity'],
                            2
                        ) ?>

                        <?= htmlspecialchars(
                            $crop['unit']
                            ?? 'kg'
                        ) ?>
                    </strong>

                </p>


                <?php if (
                    isset($_SESSION['role'])
                    &&
                    strtolower(
                        $_SESSION['role']
                    )
                    === 'customer'
                ): ?>


                    <form
                        action="index.php?page=cart-add"
                        method="POST"
                        class="add-cart-form"
                    >

                        <input
                            type="hidden"
                            name="crop_id"
                            value="<?= (int)$crop['crop_id'] ?>"
                        >


                        <label>
                            Quantity
                        </label>


                        <input
                            type="number"
                            name="quantity"
                            value="1"
                            min="1"
                            step="1"
                            max="<?= (int)floor((float)$crop['quantity']) ?>"
                            required
                        >


                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Add to Cart
                        </button>

                    </form>


                <?php else: ?>


                    <a
                        href="index.php?page=login"
                        class="btn btn-primary"
                    >
                        Login to Buy
                    </a>


                <?php endif; ?>


            </div>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>