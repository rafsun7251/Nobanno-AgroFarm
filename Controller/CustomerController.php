<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Model/Crop.php';
require_once __DIR__ . '/../Model/Order.php';
require_once __DIR__ . '/../Model/OrderItem.php';
require_once __DIR__ . '/../Model/Cart.php';


class CustomerController extends BaseController
{
    private Customer $customerModel;
    private Crop $cropModel;
    private Order $orderModel;
    private OrderItem $orderItemModel;
    private Cart $cartModel;


    public function __construct()
    {
        $this->customerModel =
            new Customer();

        $this->cropModel =
            new Crop();

        $this->orderModel =
            new Order();

        $this->orderItemModel =
            new OrderItem();

        $this->cartModel =
            new Cart();
    }


    // ==========================================
    // DASHBOARD
    // ==========================================

    public function dashboard(): void
    {
        $this->requireRole('customer');


        $customer =
            $this->customerModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$customer) {

            $this->setMessage(
                'error',
                'Customer profile not found.'
            );

            $this->redirect(
                'index.php?page=login'
            );
        }


        $crops =
            $this->cropModel
                ->getActive();


        $orders =
            $this->orderModel
                ->getByCustomer(
                    $customer['customer_id']
                );


        $cartCount =
            $this->cartModel
                ->countItems(
                    $customer['customer_id']
                );


        $this->view(
            'customer/dashboard',
            [
                'customer' =>
                    $customer,

                'crops' =>
                    $crops,

                'orders' =>
                    $orders,

                'cartCount' =>
                    $cartCount
            ]
        );
    }


    // ==========================================
    // PRODUCTS
    // ==========================================

    public function crops(): void
    {
        $this->requireRole('customer');


        $keyword =
            trim(
                $_GET['search'] ?? ''
            );

        $category =
            trim(
                $_GET['category'] ?? ''
            );


        $crops =
            $this->cropModel
                ->search(
                    $keyword,
                    $category
                );


        $this->view(
            'home/crops',
            [
                'crops' =>
                    $crops,

                'keyword' =>
                    $keyword,

                'category' =>
                    $category
            ]
        );
    }


    // ==========================================
    // PRODUCT DETAILS
    // ==========================================

    public function productDetails(): void
    {
        $this->requireRole('customer');


        $cropId =
            (int)(
                $_GET['id'] ?? 0
            );


        if ($cropId <= 0) {

            $this->setMessage(
                'error',
                'Invalid product.'
            );

            $this->redirect(
                'index.php?page=customer-crops'
            );
        }


        $crop =
            $this->cropModel
                ->findById($cropId);


        if (!$crop) {

            $this->setMessage(
                'error',
                'Product not found.'
            );

            $this->redirect(
                'index.php?page=customer-crops'
            );
        }


        $this->view(
            'home/product_details',
            [
                'crop' => $crop
            ]
        );
    }


    // ==========================================
    // PROFILE
    // ==========================================

    public function profile(): void
    {
        $this->requireRole('customer');


        $customer =
            $this->customerModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$customer) {
            $this->redirect('index.php?page=logout');
        }

        $this->view(
            'customer/profile',
            [
                'customer' =>
                    $customer
            ]
        );
    }


    // ==========================================
    // UPDATE PROFILE
    // ==========================================

    public function updateProfile(): void
    {
        $this->requireRole('customer');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=customer-profile');
        }


        $customer =
            $this->customerModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$customer) {

            $this->setMessage(
                'error',
                'Customer profile not found.'
            );

            $this->redirect(
                'index.php?page=customer-profile'
            );
        }


        $address =
            trim(
                $_POST['address'] ?? ''
            );


        $updated =
            $this->customerModel
                ->updateAddress(
                    $customer['customer_id'],
                    $address
                );


        $this->setMessage(
            $updated
                ? 'success'
                : 'error',
            $updated
                ? 'Profile updated successfully.'
                : 'Failed to update profile.'
        );


        $this->redirect(
            'index.php?page=customer-profile'
        );
    }


    // ==========================================
    // ORDERS
    // ==========================================

    public function orders(): void
    {
        $this->requireRole('customer');


        $customer =
            $this->customerModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$customer) {

            $this->redirect(
                'index.php?page=login'
            );
        }


        $orders =
            $this->orderModel
                ->getByCustomer(
                    $customer['customer_id']
                );


        $this->view(
            'customer/orders',
            [
                'orders' =>
                    $orders
            ]
        );
    }


    // ==========================================
    // ORDER DETAILS
    // ==========================================

    public function orderDetails(): void
    {
        $this->requireRole('customer');


        $orderId =
            (int)(
                $_GET['id'] ?? 0
            );


        $customer =
            $this->customerModel
                ->findByUserId(
                    $this->userId()
                );


        $order =
            $this->orderModel
                ->findById(
                    $orderId
                );


        if (
            !$customer
            ||
            !$order
            ||
            (int)$order['customer_id']
            !==
            (int)$customer['customer_id']
        ) {

            $this->setMessage(
                'error',
                'Order not found.'
            );

            $this->redirect(
                'index.php?page=customer-orders'
            );
        }


        $items =
            $this->orderItemModel
                ->getByOrder(
                    $orderId
                );


        $this->view(
            'customer/order_details',
            [
                'order' =>
                    $order,

                'items' =>
                    $items
            ]
        );
    }
}