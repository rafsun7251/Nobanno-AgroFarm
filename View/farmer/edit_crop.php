<?php
$pageTitle = 'Edit Crop';

$extraCss = [
    'public/css/farmer.css'
];

require __DIR__ . '/../layouts/header.php';

$selectedCategory = strtolower($crop['category'] ?? '');
?>

<main class="form-page">

    <div class="page-container">

        <div class="form-card">

            <div class="page-header">
                <h1>Edit Crop</h1>
                <p>Update the details of your product.</p>
            </div>

            <form
                action="index.php?page=farmer-update-crop"
                method="POST"
                class="main-form"
            >

                <input
                    type="hidden"
                    name="crop_id"
                    value="<?= (int)$crop['crop_id'] ?>"
                >

                <div class="form-row">

                    <div class="form-group">
                        <label for="crop_name">Crop Name</label>
                        <input
                            type="text"
                            id="crop_name"
                            name="crop_name"
                            placeholder="e.g. Tomato"
                            value="<?= e($crop['crop_name'] ?? '') ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>

                            <option value="">Select category</option>

                            <?php foreach (
                                FarmerController::CATEGORIES
                                as $value => $label
                            ): ?>
                                <option
                                    value="<?= e($value) ?>"
                                    <?= $selectedCategory === $value ? 'selected' : '' ?>
                                >
                                    <?= e($label) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>


                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        placeholder="Describe your product..."
                    ><?= e($crop['description'] ?? '') ?></textarea>
                </div>


                <div class="form-row">

                    <div class="form-group">
                        <label for="price_per_kg">Price per KG</label>
                        <input
                            type="number"
                            id="price_per_kg"
                            name="price_per_kg"
                            min="0.01"
                            step="0.01"
                            value="<?= e($crop['price_per_kg'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="0"
                            step="0.01"
                            value="<?= e($crop['quantity'] ?? '') ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="unit">Unit</label>
                        <select id="unit" name="unit">
                            <?php foreach (FarmerController::UNITS as $u): ?>
                                <option
                                    value="<?= e($u) ?>"
                                    <?= ($crop['unit'] ?? 'kg') === $u ? 'selected' : '' ?>
                                >
                                    <?= e(strtoupper($u)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                </div>


                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach (FarmerController::STATUSES as $st): ?>
                            <option
                                value="<?= e($st) ?>"
                                <?= ($crop['status'] ?? '') === $st ? 'selected' : '' ?>
                            >
                                <?= e(ucfirst($st)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="form-actions">

                    <a
                        href="index.php?page=farmer-crops"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</main>

<?php
require __DIR__ . '/../layouts/footer.php';
?>
