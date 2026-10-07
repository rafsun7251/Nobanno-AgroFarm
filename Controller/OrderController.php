<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Model/Cart.php';
require_once __DIR__ . '/../Model/Crop.php';
require_once __DIR__ . '/../Model/Order.php';
require_once __DIR__ . '/../Model/OrderItem.php';
require_once __DIR__ . '/../Model/Payment.php';


class OrderController extends BaseController
{
    // Delivery charge is decided by the server. It used to be
    // read from a hidden <input> (value="0"), so a customer
    // could post any amount, including a negative one.
    private const DELIVERY_CHARGE = 0.0;

    private const PAYMENT_METHODS = ['cash', 'bkash', 'card'];


    private Customer $customerModel;
    private Cart $cartModel;
    private Crop $cropModel;
    private Order $orderModel;
    private OrderItem $orderItemModel;
    private Payment $paymentModel;


    public function __construct()
    {
        $this->customerModel = new Customer();
        $this->cartModel = new Cart();
        $this->cropModel = new Crop();
        $this->orderModel = new Order();
        $this->orderItemModel = new OrderItem();
        $this->paymentModel = new Payment();
    }


    // True only when the site is opened as localhost (dev machine).
    private function isLocal(): bool
    {
        return in_array(
            $_SERVER['REMOTE_ADDR'] ?? '',
            ['127.0.0.1', '::1'],
            true
        );
    }


    private function currentCustomer(): array
    {
        $this->requireRole('customer');

        $customer = $this->customerModel
            ->findByUserId($this->userId());

        if (!$customer) {
            $this->redirect('index.php?page=logout');
        }

        return $customer;
    }


    // ==========================================
    // CHECKOUT PAGE
    // ==========================================

    public function checkout(): void
    {
        $customer = $this->currentCustomer();

        $items = $this->cartModel
            ->getByCustomer((int)$customer['customer_id']);

        if (empty($items)) {

            $this->setMessage('error', 'Your cart is empty.');

            $this->redirect('index.php?page=cart');
        }

        $total = 0.0;

        foreach ($items as $item) {
            $total +=
                (float)$item['cart_quantity']
                * (float)$item['price_per_kg'];
        }

        $this->view(
            'customer/checkout',
            [
                'customer' => $customer,
                'items'    => $items,
                'total'    => $total,
            ]
        );
    }


    // ==========================================
    // PLACE ORDER
    // ==========================================

    public function create(): void
    {
        $customer = $this->currentCustomer();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=checkout');
        }

        $customerId = (int)$customer['customer_id'];

        $items = $this->cartModel->getByCustomer($customerId);

        if (empty($items)) {

            $this->setMessage('error', 'Your cart is empty.');

            $this->redirect('index.php?page=cart');
        }

        $deliveryAddress = trim($_POST['delivery_address'] ?? '');
        $paymentMethod = strtolower(trim($_POST['payment_method'] ?? ''));
        $transactionId = trim($_POST['transaction_id'] ?? '');

        if ($deliveryAddress === '') {

            $this->setMessage('error', 'Delivery address is required.');

            $this->redirect('index.php?page=checkout');
        }

        if (
            $paymentMethod !== ''
            && !in_array($paymentMethod, self::PAYMENT_METHODS, true)
        ) {

            $this->setMessage('error', 'Invalid payment method.');

            $this->redirect('index.php?page=checkout');
        }

        // Re-validate everything against the database.

        $total = 0.0;

        foreach ($items as $item) {

            $requested = (float)$item['cart_quantity'];

            if (
                $item['status'] !== 'available'
                || $requested > (float)$item['available_quantity']
            ) {

                $this->setMessage(
                    'error',
                    'Insufficient stock for ' . $item['crop_name']
                );

                $this->redirect('index.php?page=cart');
            }

            $total += $requested * (float)$item['price_per_kg'];
        }

        $deliveryCharge = self::DELIVERY_CHARGE;
        $grandTotal = $total + $deliveryCharge;

        // Order + items + stock + payment + cart clearing must
        // succeed or fail together. (All models now share one
        // connection, so one transaction covers all of them.)

        $db = (new Database())->connect();

        $db->begin_transaction();

        try {

            $orderId = $this->orderModel->create(
                $customerId,
                $grandTotal,
                $deliveryCharge,
                $deliveryAddress
            );

            if (!$orderId) {
                throw new RuntimeException('order');
            }

            foreach ($items as $item) {

                $itemId = $this->orderItemModel->create(
                    $orderId,
                    (int)$item['crop_id'],
                    (float)$item['cart_quantity'],
                    (float)$item['price_per_kg']
                );

                if (!$itemId) {
                    throw new RuntimeException('item');
                }

                // Atomic stock decrement: fails if someone else
                // bought the stock in the meantime.

                if (
                    !$this->cropModel->decreaseQuantity(
                        (int)$item['crop_id'],
                        (float)$item['cart_quantity']
                    )
                ) {
                    throw new RuntimeException('stock');
                }
            }

            if ($paymentMethod !== '') {

                $paymentId = $this->paymentModel->create(
                    $orderId,
                    $customerId,
                    $grandTotal,
                    $paymentMethod,
                    $transactionId
                );

                if (!$paymentId) {
                    throw new RuntimeException('payment');
                }
            }

            $this->cartModel->clear($customerId);

            $db->commit();

        } catch (Throwable $e) {

            $db->rollback();

            // Real reason goes to the PHP error log
            // (XAMPP: C:\xampp\apache\logs\error.log).
            error_log(
                'Order failed [' . $e->getMessage() . '] '
                . get_class($e)
            );

            $this->setMessage(
                'error',
                $e->getMessage() === 'stock'
                    ? 'Some items just went out of stock. '
                      . 'Please review your cart.'
                    : 'Failed to place the order. Please try again.'
                      . ($this->isLocal() ? ' [DEBUG: ' . $e->getMessage() . ']' : '')
            );

            $this->redirect('index.php?page=cart');
        }

        $this->setMessage('success', 'Order placed successfully.');

        $this->redirect('index.php?page=customer-orders');
    }
}
