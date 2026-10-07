<?php

$pageTitle = 'Manage Users';

$extraCss = [
    'public/css/admin.css'
];

require __DIR__ . '/../layouts/header.php';

?>

<main class="dashboard-page">

    <div class="page-container">


        <div class="page-header">

            <h1>
                Manage Users
            </h1>

        </div>


        <div class="table-container">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Role
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach (
                        $users
                        as $user
                    ): ?>

                        <tr>

                            <td>
                                <?= (int)$user[
                                    'user_id'
                                ] ?>
                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    ($user[
                                        'first_name'
                                    ]
                                    ?? '')
                                    . ' '
                                    .
                                    ($user[
                                        'last_name'
                                    ]
                                    ?? '')
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $user['email']
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $user[
                                        'phone'
                                    ]
                                    ?? ''
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $user[
                                        'role'
                                    ]
                                ) ?>

                            </td>


                            <td>

                                <?php if (
                                    (int)$user[
                                        'user_id'
                                    ]
                                    !==
                                    (int)$_SESSION[
                                        'user_id'
                                    ]
                                ): ?>

                                    <form
                                        action="index.php?page=admin-delete-user"
                                        method="POST"
                                        class="inline-form"
                                        onsubmit="return confirm('Delete this user?')"
                                    >
                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?= (int)$user['user_id'] ?>"
                                        >
                                        <button
                                            type="submit"
                                            class="btn btn-small btn-danger"
                                        >
                                            Delete
                                        </button>
                                    </form>

                                <?php else: ?>

                                    Current User

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</main>


<?php
require __DIR__ . '/../layouts/footer.php';
?>