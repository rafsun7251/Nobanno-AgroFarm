<?php
/*
 * Shared admin table.
 *
 * Expects:
 *   $title    string
 *   $subtitle string (optional)
 *   $columns  array  label => 'row_key'  OR  label => callable($row): string
 *             (a callable must return already-escaped HTML)
 *   $rows     array
 */

$pageTitle = $title;

$extraCss = [
    'public/css/admin.css'
];

require __DIR__ . '/../layouts/header.php';
?>

<main class="dashboard-page">

    <div class="page-container">

        <div class="page-header">
            <div>
                <h1><?= e($title) ?></h1>

                <?php if (!empty($subtitle)): ?>
                    <p><?= e($subtitle) ?></p>
                <?php endif; ?>
            </div>

            <a href="index.php?page=admin-dashboard" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>

        <div class="table-container">
            <table>

                <thead>
                    <tr>
                        <?php foreach ($columns as $label => $source): ?>
                            <th><?= e($label) ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($rows)): ?>

                        <tr>
                            <td
                                colspan="<?= count($columns) ?>"
                                class="text-center"
                            >
                                Nothing to show yet.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($columns as $label => $source): ?>
                                    <td>
                                        <?= is_callable($source)
                                            ? $source($row)
                                            : e($row[$source] ?? '') ?>
                                    </td>
                                <?php endforeach; ?>
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
