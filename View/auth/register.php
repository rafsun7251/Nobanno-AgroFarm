<?php

$pageTitle = 'Register';

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
                    Create Account
                </h1>

                <p>
                    Join Nobanno Agro Farm
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
                action="index.php?page=register-process"
                method="POST"
                class="auth-form"
            >

                <div class="form-row">

                    <div class="form-group">

                        <label>
                            First Name
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Last Name
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Phone
                    </label>

                    <input
                        type="tel"
                        name="phone"
                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Address
                    </label>

                    <textarea
                        name="address"
                        rows="3"
                    ></textarea>

                </div>


                <div class="form-row">

                    <div class="form-group">

                        <label>
                            Password
                        </label>

                        <input
                            type="password"
                            name="password"
                            minlength="6"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            required
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Create Account
                </button>

            </form>


            <div class="auth-footer">

                <p>
                    Already have an account?

                    <a
                        href="index.php?page=login"
                    >
                        Login
                    </a>
                </p>

            </div>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>