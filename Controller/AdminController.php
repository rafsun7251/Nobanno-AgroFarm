<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/User.php';
require_once __DIR__ . '/../Model/Farmer.php';
require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Model/DeliveryMan.php';
require_once __DIR__ . '/../Model/Crop.php';
require_once __DIR__ . '/../Model/Order.php';
require_once __DIR__ . '/../Model/OrderItem.php';
require_once __DIR__ . '/../Model/Payment.php';
require_once __DIR__ . '/../Model/Delivery.php';


class AdminController extends BaseController
{
    private User $userModel;
    private Farmer $farmerModel;
    private Customer $customerModel;
    private DeliveryMan $deliveryManModel;
    private Crop $cropModel;
    private Order $orderModel;
    private OrderItem $orderItemModel;
    private Payment $paymentModel;
    private Delivery $deliveryModel;


    public function __construct()
    {
        $this->userModel = new User();
        $this->farmerModel = new Farmer();
        $this->customerModel = new Customer();
        $this->deliveryManModel = new DeliveryMan();
        $this->cropModel = new Crop();
        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->paymentModel = new Payment();
        $this->deliveryModel = new Delivery();
    }


    // ==========================================
    // DASHBOARD
    // ==========================================

    public function dashboard(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/dashboard',
            [
                'totalUsers' =>
                    $this->userModel->countAll(),

                'totalFarmers' =>
                    $this->farmerModel->countAll(),

                'totalCustomers' =>
                    $this->customerModel->countAll(),

                'totalDeliveryMen' =>
                    $this->deliveryManModel->countAll(),

                'totalCrops' =>
                    $this->cropModel->countAll(),

                'totalOrders' =>
                    $this->orderModel->countAll(),

                'totalRevenue' =>
                    $this->paymentModel->totalRevenue(),

                'totalDeliveries' =>
                    $this->deliveryModel->countAll()
            ]
        );
    }


    // ==========================================
    // USERS
    // ==========================================

    public function users(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/users',
            [
                'users' =>
                    $this->userModel->getAll()
            ]
        );
    }


    public function deleteUser(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin-users');
        }

        $userId = (int)($_POST['user_id'] ?? 0);

        if ($userId <= 0) {

            $this->setMessage('error', 'Invalid user.');

            $this->redirect('index.php?page=admin-users');
        }

        // An admin must not delete his own account.

        if ($userId === $this->userId()) {

            $this->setMessage(
                'error',
                'You cannot delete your own account.'
            );

            $this->redirect('index.php?page=admin-users');
        }

        $deleted = $this->userModel->delete($userId);

        $this->setMessage(
            $deleted ? 'success' : 'error',
            $deleted
                ? 'User deleted successfully.'
                : 'Failed to delete user. '
                  . 'The user may have related records.'
        );

        $this->redirect('index.php?page=admin-users');
    }


    // ==========================================
    // LISTS
    // ==========================================

    public function farmers(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/farmers',
            [
                'farmers' =>
                    $this->farmerModel->getAll()
            ]
        );
    }


    public function customers(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/customers',
            [
                'customers' =>
                    $this->customerModel->getAll()
            ]
        );
    }


    public function deliveryMen(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/delivery_man',
            [
                'deliveryMen' =>
                    $this->deliveryManModel->getAll()
            ]
        );
    }


    public function crops(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/crops',
            [
                'crops' =>
                    $this->cropModel->getAll()
            ]
        );
    }


    public function orders(): void
    {
        $this->requireRole('admin');

        $this->view(
            'admin/orders',
            [
                'orders' =>
                    $this->orderModel->getAll()
            ]
        );
    }


    public function orderDetails(): void
    {
        $this->requireRole('admin');

        $orderId = (int)($_GET['id'] ?? 0);

        $order = $this->orderModel->findById($orderId);

        if (!$order) {

            $this->setMessage('error', 'Order not found.');

            $this->redirect('index.php?page=admin-orders');
        }

        $this->view(
            'admin/order_details',
            [
                'order' => $order,

                'items' =>
                    $this->orderItemModel->getByOrder($orderId),

                'payment' =>
                    $this->paymentModel->findByOrder($orderId)
            ]
        );
    }
}
