<?php

$pageTitle = 'Unauthorized';

require __DIR__ . '/../layouts/header.php';

?>

<main class="error-page">

    <div class="error-container">

        <h1>
            Access Denied
        </h1>

        <p>
            You do not have permission
            to access this page.
        </p>

        <a
            href="index.php"
            class="btn btn-primary"
        >
            Go Home
        </a>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>