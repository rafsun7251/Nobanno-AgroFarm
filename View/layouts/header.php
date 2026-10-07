<?php
$pageTitle = $pageTitle ?? 'Nobanno Agro Farm';

// Short HTML-escape helper used by all views.
if (!function_exists('e')) {
    function e($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

// Flash message (success / error) set by any controller.
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($pageTitle) ?>
        | Nobanno Agro Farm
    </title>

    <!-- Relative paths: work whatever the folder is called -->
    <link
        rel="stylesheet"
        href="public/css/style.css"
    >

    <?php if (!empty($extraCss)): ?>

        <?php foreach ($extraCss as $css): ?>

            <link
                rel="stylesheet"
                href="<?= e($css) ?>"
            >

        <?php endforeach; ?>

    <?php endif; ?>

</head>

<body>

<?php
require __DIR__ . '/navbar.php';
?>

<?php if (!empty($flash['message'])): ?>

    <div class="flash-wrap">
        <div class="alert alert-<?= e($flash['type'] ?? 'info') ?>">
            <?= e($flash['message']) ?>
        </div>
    </div>

<?php endif; ?>
