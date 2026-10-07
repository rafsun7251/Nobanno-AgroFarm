<?php
$pageTitle = 'My Profile';

$extraCss = [
    'public/css/customer.css'
];

require __DIR__ . '/../layouts/header.php';
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <h1>My Profile</h1>
        </div>

        <div class="form-card">

            <dl class="detail-list">
                <dt>Name</dt>
                <dd><?= e(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?></dd>

                <dt>Email</dt>
                <dd><?= e($customer['email'] ?? '') ?></dd>

                <dt>Phone</dt>
                <dd><?= e($customer['phone'] ?? '') ?></dd>
            </dl>

            <form
                action="index.php?page=customer-update-profile"
                method="POST"
                class="main-form"
            >

                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                    ><?= e($customer['address'] ?? '') ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        Save Address
                    </button>
                </div>

            </form>

        </div>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
