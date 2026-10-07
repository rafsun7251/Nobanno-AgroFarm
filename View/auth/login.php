<?php

$pageTitle = 'Login';

$extraCss = [
    'public/css/auth.css'
];

require __DIR__ . '/../layouts/header.php';

$message =
    $message ?? null;

?>

<main class="auth-page">

    <div class="auth-container">


        <div class="auth-card">

            <div class="auth-header">

                <h1>
                    Welcome Back
                </h1>

                <p>
                    Login to your Nobanno
                    Agro Farm account.
                </p>

            </div>


            <?php if ($message): ?>

                <div
                    class="alert alert-<?= e($message['type']) ?>"
                >

                    <?= htmlspecialchars(
                        $message['message']
                    ) ?>

                </div>

            <?php endif; ?>


            <form
                action="index.php?page=login-process"
                method="POST"
                class="auth-form"
            >

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Login
                </button>

            </form>


            <div class="auth-footer">

                <p>
                    Don't have an account?
                    <a
                        href="index.php?page=register"
                    >
                        Register
                    </a>
                </p>

                <p>
                    Are you a farmer?
                    <a
                        href="index.php?page=become-farmer"
                    >
                        Join as Farmer
                    </a>
                </p>

            </div>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>