<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Farmer.php';
require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Model/DeliveryMan.php';


class AuthController extends BaseController
{
    private User $userModel;
    private Farmer $farmerModel;
    private Customer $customerModel;
    private DeliveryMan $deliveryModel;


    public function __construct()
    {
        $this->userModel =
            new User();

        $this->farmerModel =
            new Farmer();

        $this->customerModel =
            new Customer();

        $this->deliveryModel =
            new DeliveryMan();
    }


    // ==========================================
    // LOGIN PAGE
    // ==========================================

    public function login(): void
    {
        if (
            isset(
                $_SESSION['user_id'],
                $_SESSION['role']
            )
        ) {

            $this->redirectByRole();
        }

        $message =
            $this->getMessage();

        $this->view(
            'auth/login',
            [
                'message' => $message
            ]
        );
    }


    // ==========================================
    // LOGIN PROCESS
    // ==========================================

    public function authenticate(): void
    {
        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            $this->redirect(
                'index.php?page=login'
            );
        }


        $email =
            trim(
                $_POST['email'] ?? ''
            );

        $password =
            $_POST['password'] ?? '';


        // Validation

        if (
            $email === ''
            ||
            $password === ''
        ) {

            $this->setMessage(
                'error',
                'Please enter both email and password.'
            );

            $this->redirect(
                'index.php?page=login'
            );
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $this->setMessage(
                'error',
                'Please enter a valid email address.'
            );

            $this->redirect(
                'index.php?page=login'
            );
        }


        // Authenticate through Model

        $user =
            $this->userModel
                ->authenticate(
                    $email,
                    $password
                );


        if (!$user) {

            $this->setMessage(
                'error',
                'Invalid email or password.'
            );

            $this->redirect(
                'index.php?page=login'
            );
        }


        // Regenerate session

        session_regenerate_id(true);


        $_SESSION['user_id'] =
            $user['user_id'];

        $_SESSION['first_name'] =
            $user['first_name'];

        $_SESSION['last_name'] =
            $user['last_name'];

        $_SESSION['email'] =
            $user['email'];

        $_SESSION['phone'] =
            $user['phone'] ?? '';

        $_SESSION['role'] =
            strtolower(
                trim(
                    $user['role']
                )
            );


        $this->redirectByRole();
    }


    // ==========================================
    // REGISTER PAGE
    // ==========================================

    public function register(): void
    {
        if (isset($_SESSION['user_id'], $_SESSION['role'])) {
            $this->redirectByRole();
        }

        $this->view(
            'auth/register',
            [
                'message' => $this->getMessage()
            ]
        );
    }


    // ==========================================
    // REGISTER PROCESS
    // ==========================================

    public function createAccount(): void
    {
        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            $this->redirect(
                'index.php?page=register'
            );
        }


        $firstName =
            trim(
                $_POST['first_name'] ?? ''
            );

        $lastName =
            trim(
                $_POST['last_name'] ?? ''
            );

        $email =
            trim(
                $_POST['email'] ?? ''
            );

        $password =
            $_POST['password'] ?? '';

        $confirmPassword =
            $_POST['confirm_password']
            ?? '';

        $phone =
            trim(
                $_POST['phone'] ?? ''
            );

        $role =
            strtolower(
                trim(
                    $_POST['role'] ?? 'customer'
                )
            );


        // Basic validation

        if (
            $firstName === ''
            ||
            $lastName === ''
            ||
            $email === ''
            ||
            $password === ''
            ||
            $phone === ''
        ) {

            $this->setMessage(
                'error',
                'Please fill in all required fields.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $this->setMessage(
                'error',
                'Invalid email address.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        if (
            $password !== $confirmPassword
        ) {

            $this->setMessage(
                'error',
                'Passwords do not match.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        if (strlen($password) < 6) {

            $this->setMessage(
                'error',
                'Password must be at least 6 characters.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        if (
            $this->userModel
                ->emailExists($email)
        ) {

            $this->setMessage(
                'error',
                'Email already exists.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        // Public registration
        // should only create customer

        $role = 'customer';


        $userId =
            $this->userModel
                ->create(
                    $firstName,
                    $lastName,
                    $email,
                    $password,
                    $role,
                    $phone
                );


        if (!$userId) {

            $this->setMessage(
                'error',
                'Failed to create account.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        $address =
            trim(
                $_POST['address'] ?? ''
            );


        $customerCreated =
            $this->customerModel
                ->create(
                    $userId,
                    $address
                );


        if (!$customerCreated) {

            $this->userModel
                ->delete($userId);

            $this->setMessage(
                'error',
                'Failed to create customer profile.'
            );

            $this->redirect(
                'index.php?page=register'
            );
        }


        $this->setMessage(
            'success',
            'Registration successful. Please login.'
        );

        $this->redirect(
            'index.php?page=login'
        );
    }


    // ==========================================
    // FARMER REGISTRATION
    // ==========================================

    public function registerFarmer(): void
    {
        if (isset($_SESSION['user_id'], $_SESSION['role'])) {
            $this->redirectByRole();
        }

        $this->view(
            'auth/farmer_register',
            [
                'message' => $this->getMessage()
            ]
        );
    }


    // ==========================================
    // FARMER REGISTRATION PROCESS
    // ==========================================

    public function createFarmer(): void
    {
        if (
            $_SERVER['REQUEST_METHOD']
            !== 'POST'
        ) {

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        $firstName =
            trim(
                $_POST['first_name'] ?? ''
            );

        $lastName =
            trim(
                $_POST['last_name'] ?? ''
            );

        $email =
            trim(
                $_POST['email'] ?? ''
            );

        $password =
            $_POST['password'] ?? '';

        $confirmPassword =
            $_POST['confirm_password']
            ?? '';

        $phone =
            trim(
                $_POST['phone'] ?? ''
            );

        $farmName =
            trim(
                $_POST['farm_name'] ?? ''
            );

        $location =
            trim(
                $_POST['location'] ?? ''
            );

        $address =
            trim(
                $_POST['address'] ?? ''
            );


        if (
            $firstName === ''
            ||
            $lastName === ''
            ||
            $email === ''
            ||
            $password === ''
            ||
            $phone === ''
            ||
            $farmName === ''
        ) {

            $this->setMessage(
                'error',
                'Please fill in all required fields.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        if (
            !filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {

            $this->setMessage(
                'error',
                'Invalid email address.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        if ($password !== $confirmPassword) {

            $this->setMessage(
                'error',
                'Passwords do not match.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        if (strlen($password) < 6) {

            $this->setMessage(
                'error',
                'Password must be at least 6 characters.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        if (
            $this->userModel
                ->emailExists($email)
        ) {

            $this->setMessage(
                'error',
                'Email already exists.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        $userId =
            $this->userModel
                ->create(
                    $firstName,
                    $lastName,
                    $email,
                    $password,
                    'farmer',
                    $phone
                );


        if (!$userId) {

            $this->setMessage(
                'error',
                'Failed to create farmer account.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        $created =
            $this->farmerModel
                ->create(
                    $userId,
                    $farmName,
                    $location,
                    $address
                );


        if (!$created) {

            $this->userModel
                ->delete($userId);

            $this->setMessage(
                'error',
                'Failed to create farmer profile.'
            );

            $this->redirect(
                'index.php?page=farmer-register'
            );
        }


        $this->setMessage(
            'success',
            'Farmer registration successful.'
        );

        $this->redirect(
            'index.php?page=login'
        );
    }


    // ==========================================
    // LOGOUT
    // ==========================================

    public function logout(): void
    {
        $_SESSION = [];

        if (
            ini_get('session.use_cookies')
        ) {

            $params =
                session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header(
            'Location: index.php?page=login'
        );

        exit();
    }


    // ==========================================
    // ROLE REDIRECTION
    // ==========================================

    private function redirectByRole(): void
    {
        $role =
            strtolower(
                trim(
                    $_SESSION['role'] ?? ''
                )
            );


        switch ($role) {

            case 'admin':

                $this->redirect(
                    'index.php?page=admin-dashboard'
                );

                break;


            case 'farmer':

                $this->redirect(
                    'index.php?page=farmer-dashboard'
                );

                break;


            case 'delivery_man':
            case 'delivery':

                $this->redirect(
                    'index.php?page=delivery-dashboard'
                );

                break;


            case 'customer':

                $this->redirect(
                    'index.php?page=customer-dashboard'
                );

                break;


            default:

                $this->logout();
        }
    }
}