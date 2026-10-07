<?php

$pageTitle = 'Checkout';

$extraCss = [
    'public/css/cart.css'
];

$extraJs = [
    'public/js/checkout.js'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="checkout-page">

    <div class="page-container">

        <div class="page-header">

            <h1>
                Checkout
            </h1>

        </div>


        <form
            action="index.php?page=create-order"
            method="POST"
            class="checkout-form"
        >


            <div class="checkout-grid">


                <section class="checkout-section">

                    <h2>
                        Delivery Information
                    </h2>


                    <div class="form-group">

                        <label>
                            Full Name
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                ($customer['first_name']
                                ?? '')
                                . ' '
                                .
                                ($customer['last_name']
                                ?? '')
                            ) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Phone
                        </label>

                        <input
                            type="text"
                            value="<?= htmlspecialchars(
                                $customer['phone']
                                ?? ''
                            ) ?>"
                            readonly
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Delivery Address
                        </label>

                        <textarea
                            name="delivery_address"
                            rows="4"
                            required
                        ><?= htmlspecialchars(
                            $customer['address']
                            ?? ''
                        ) ?></textarea>

                    </div>


                    <div class="form-group">

                        <label>
                            Payment Method
                        </label>

                        <select
                            name="payment_method"
                            id="payment_method"
                            required
                        >

                            <option value="">
                                Select Payment Method
                            </option>

                            <option value="cash">
                                Cash on Delivery
                            </option>

                            <option value="bkash">
                                bKash
                            </option>

                            <option value="card">
                                Card
                            </option>

                        </select>

                    </div>


                    <div
                        class="form-group"
                        id="transaction-group"
                        style="display:none;"
                    >

                        <label>
                            Transaction ID
                        </label>

                        <input
                            type="text"
                            name="transaction_id"
                            id="transaction_id"
                            placeholder="Enter transaction ID"
                        >

                    </div>
</section>


                <aside class="order-summary">

                    <h2>
                        Your Order
                    </h2>


                    <?php foreach (
                        $items
                        as $item
                    ): ?>

                        <div class="summary-item">

                            <span>

                                <?= htmlspecialchars(
                                    $item['crop_name']
                                ) ?>

                                ×

                                <?= (int)$item[
                                    'cart_quantity'
                                ] ?>

                            </span>


                            <strong>

                                ৳<?= number_format(
                                    (float)$item[
                                        'price_per_kg'
                                    ]
                                    *
                                    (float)$item[
                                        'cart_quantity'
                                    ],
                                    2
                                ) ?>

                            </strong>

                        </div>

                    <?php endforeach; ?>


                    <hr>


                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>

                            ৳<?= number_format(
                                $total,
                                2
                            ) ?>

                        </strong>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-primary btn-full"
                    >
                        Place Order
                    </button>

                </aside>

            </div>

        </form>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>