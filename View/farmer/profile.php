<?php
$pageTitle = 'My Profile';

$extraCss = [
    'public/css/farmer.css'
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
                <dd><?= e(($farmer['first_name'] ?? '') . ' ' . ($farmer['last_name'] ?? '')) ?></dd>

                <dt>Email</dt>
                <dd><?= e($farmer['email'] ?? '') ?></dd>

                <dt>Phone</dt>
                <dd><?= e($farmer['phone'] ?? '') ?></dd>

                <dt>Farm Name</dt>
                <dd><?= e($farmer['farm_name'] ?? '') ?></dd>

                <dt>Location</dt>
                <dd><?= e($farmer['location'] ?? '') ?></dd>

                <dt>Address</dt>
                <dd><?= e($farmer['address'] ?? '') ?></dd>

            </dl>

        </div>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
