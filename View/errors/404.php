<?php

$pageTitle = 'Page Not Found';

require __DIR__ . '/../layouts/header.php';

?>

<main class="error-page">

    <div class="error-container">

        <h1>
            404
        </h1>

        <h2>
            Page Not Found
        </h2>

        <p>
            The page you are looking for
            does not exist.
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