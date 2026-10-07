<?php

// ==========================================
// SESSION
// ==========================================

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();


// ==========================================
// CONTROLLERS
// ==========================================

require_once __DIR__ . '/Controller/BaseController.php';

$controllerFiles = [
    'HomeController',
    'AuthController',
    'CropController',
    'CustomerController',
    'CartController',
    'OrderController',
    'FarmerController',
    'AdminController',
    'PaymentController',
    'DeliveryController',
];

foreach ($controllerFiles as $file) {
    require_once __DIR__ . '/Controller/' . $file . '.php';
}


// ==========================================
// ROUTES        page => [Controller, method]
// ==========================================
// Every form action / link in the views must exist here.
// (login-process, register-process, farmer-register, cart-*,
//  admin-*, delivery-* ... were missing before, so those
//  forms and links all ended on the home page.)

$routes = [

    // ---- Public
    'home'            => ['HomeController', 'index'],
    'unauthorized'    => ['HomeController', 'unauthorized'],
    'crops'           => ['CropController', 'index'],
    'crop-search'     => ['CropController', 'search'],
    'product-details' => ['CropController', 'details'],

    // ---- Auth
    'login'                   => ['AuthController', 'login'],
    'login-process'           => ['AuthController', 'authenticate'],
    'register'                => ['AuthController', 'register'],
    'register-process'        => ['AuthController', 'createAccount'],
    'farmer-register'         => ['AuthController', 'registerFarmer'],
    'farmer-register-process' => ['AuthController', 'createFarmer'],
    'logout'                  => ['AuthController', 'logout'],
    'become-farmer'           => ['FarmerController', 'becomeFarmer'],

    // ---- Customer
    'customer-dashboard'     => ['CustomerController', 'dashboard'],
    'customer-crops'         => ['CustomerController', 'crops'],
    'customer-profile'       => ['CustomerController', 'profile'],
    'customer-update-profile'=> ['CustomerController', 'updateProfile'],
    'customer-orders'        => ['CustomerController', 'orders'],
    'customer-order-details' => ['CustomerController', 'orderDetails'],

    // ---- Cart / checkout
    'cart'         => ['CartController', 'index'],
    'cart-add'     => ['CartController', 'add'],
    'cart-update'  => ['CartController', 'update'],
    'cart-remove'  => ['CartController', 'remove'],
    'cart-clear'   => ['CartController', 'clear'],
    'checkout'     => ['OrderController', 'checkout'],
    'create-order' => ['OrderController', 'create'],

    // ---- Farmer
    'farmer-dashboard'   => ['FarmerController', 'dashboard'],
    'farmer-crops'       => ['FarmerController', 'crops'],
    'farmer-add-crop'    => ['FarmerController', 'addCrop'],
    'farmer-create-crop' => ['FarmerController', 'createCrop'],
    'farmer-edit-crop'   => ['FarmerController', 'editCrop'],
    'farmer-update-crop' => ['FarmerController', 'updateCrop'],
    'farmer-delete-crop' => ['FarmerController', 'deleteCrop'],
    'farmer-inventory'   => ['FarmerController', 'inventory'],
    'farmer-sell'        => ['FarmerController', 'sell'],
    'farmer-sales'       => ['FarmerController', 'sales'],
    'farmer-orders'      => ['FarmerController', 'orders'],
    'farmer-profile'     => ['FarmerController', 'profile'],

    // ---- Admin
    'admin-dashboard'        => ['AdminController', 'dashboard'],
    'admin-users'            => ['AdminController', 'users'],
    'admin-delete-user'      => ['AdminController', 'deleteUser'],
    'admin-farmers'          => ['AdminController', 'farmers'],
    'admin-customers'        => ['AdminController', 'customers'],
    'admin-delivery-men'     => ['AdminController', 'deliveryMen'],
    'admin-crops'            => ['AdminController', 'crops'],
    'admin-orders'           => ['AdminController', 'orders'],
    'admin-order-details'    => ['AdminController', 'orderDetails'],
    'admin-payments'         => ['PaymentController', 'index'],
    'admin-payment-update'   => ['PaymentController', 'update'],
    'admin-deliveries'       => ['DeliveryController', 'index'],
    'admin-assign-delivery'  => ['DeliveryController', 'assign'],

    // ---- Delivery man
    'delivery-dashboard' => ['DeliveryController', 'dashboard'],
    'delivery-pending'   => ['DeliveryController', 'pending'],
    'delivery-history'   => ['DeliveryController', 'history'],
    'delivery-edit'      => ['DeliveryController', 'edit'],
    'delivery-update'    => ['DeliveryController', 'update'],
];


// ==========================================
// DISPATCH
// ==========================================

$page = $_GET['page'] ?? 'home';

if (!is_string($page) || !isset($routes[$page])) {

    // Unknown page -> real 404 (it used to silently show home).

    (new HomeController())->notFound();

    exit;
}

[$controllerClass, $method] = $routes[$page];

// Controllers are created only when needed, so a request does
// not open every model / DB connection up front.

$controller = new $controllerClass();

$controller->$method();
