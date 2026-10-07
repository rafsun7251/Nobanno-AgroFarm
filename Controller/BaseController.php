<?php

class BaseController
{
    /**
     * Load a View and pass data to it.
     */
    protected function view(
        string $view,
        array $data = []
    ): void {

        $viewPath =
            __DIR__
            . '/../View/'
            . $view
            . '.php';

        if (!file_exists($viewPath)) {
            die("View not found: " . $view);
        }

        extract($data);

        require $viewPath;
    }


    /**
     * Redirect to another URL.
     */
    protected function redirect(
        string $url
    ): void {

        header(
            "Location: " . $url
        );

        exit();
    }


    /**
     * Check whether user is logged in.
     */
    protected function requireLogin(): void
    {
        if (
            !isset(
                $_SESSION['user_id'],
                $_SESSION['role']
            )
        ) {

            $this->redirect(
                'index.php?page=login'
            );
        }
    }


    /**
     * Check user role.
     */
    protected function requireRole(
        string|array $roles
    ): void {

        $this->requireLogin();

        $roles = is_array($roles)
            ? $roles
            : [$roles];

        $currentRole =
            strtolower(
                trim(
                    $_SESSION['role']
                )
            );

        if (!in_array(
            $currentRole,
            $roles,
            true
        )) {

            $this->redirect(
                'index.php?page=unauthorized'
            );
        }
    }


    /**
     * Get logged-in user ID.
     */
    protected function userId(): int
    {
        return (int)
            ($_SESSION['user_id'] ?? 0);
    }


    /**
     * Get logged-in user role.
     */
    protected function userRole(): string
    {
        return strtolower(
            trim(
                $_SESSION['role'] ?? ''
            )
        );
    }


    /**
     * Store flash message.
     */
    protected function setMessage(
        string $type,
        string $message
    ): void {

        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message
        ];
    }


    /**
     * Get flash message.
     */
    protected function getMessage(): ?array
    {
        if (
            !isset(
                $_SESSION['flash']
            )
        ) {
            return null;
        }

        $message =
            $_SESSION['flash'];

        unset(
            $_SESSION['flash']
        );

        return $message;
    }
}