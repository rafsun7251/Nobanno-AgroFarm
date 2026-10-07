<?php

$pageTitle = 'Update Delivery';

$extraCss = [
    'public/css/delivery.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="form-page">

    <div class="page-container">

        <div class="form-card">

            <div class="page-header">

                <h1>
                    Update Delivery
                </h1>

                <p>

                    Delivery #

                    <?= (int)$delivery[
                        'delivery_id'
                    ] ?>

                </p>

            </div>


            <div class="delivery-info">

                <p>

                    <strong>
                        Order:
                    </strong>

                    #<?= (int)$delivery[
                        'order_id'
                    ] ?>

                </p>


                <p>

                    <strong>
                        Address:
                    </strong>

                    <?= htmlspecialchars(
                        $delivery[
                            'delivery_address'
                        ]
                        ?? ''
                    ) ?>

                </p>

            </div>


            <form
                action="index.php?page=delivery-update"
                method="POST"
                class="main-form"
            >

                <input
                    type="hidden"
                    name="delivery_id"
                    value="<?= (int)$delivery['delivery_id'] ?>"
                >


                <div class="form-group">

                    <label>
                        Delivery Status
                    </label>

                    <select
                        name="delivery_status"
                        required
                    >

                        <option
                            value="assigned"
                            <?= $delivery[
                                'delivery_status'
                            ] === 'assigned'
                                ? 'selected'
                                : '' ?>
                        >
                            Assigned
                        </option>


                        <option
                            value="picked_up"
                            <?= $delivery[
                                'delivery_status'
                            ] === 'picked_up'
                                ? 'selected'
                                : '' ?>
                        >
                            Picked Up
                        </option>


                        <option
                            value="out_for_delivery"
                            <?= $delivery[
                                'delivery_status'
                            ] === 'out_for_delivery'
                                ? 'selected'
                                : '' ?>
                        >
                            Out for Delivery
                        </option>


                        <option
                            value="delivered"
                            <?= $delivery[
                                'delivery_status'
                            ] === 'delivered'
                                ? 'selected'
                                : '' ?>
                        >
                            Delivered
                        </option>

                    </select>

                </div>


                <div class="form-actions">

                    <a
                        href="index.php?page=delivery-pending"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Status
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>