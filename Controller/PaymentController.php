<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Payment.php';
require_once __DIR__ . '/../Model/Order.php';


class PaymentController extends BaseController
{
    private Payment $paymentModel;
    private Order $orderModel;


    public function __construct()
    {
        $this->paymentModel =
            new Payment();

        $this->orderModel =
            new Order();
    }


    // ==========================================
    // PAYMENT LIST
    // ==========================================

    public function index(): void
    {
        $this->requireRole('admin');


        $payments =
            $this->paymentModel
                ->getAll();


        $this->view(
            'admin/payments',
            [
                'payments' =>
                    $payments
            ]
        );
    }


    // ==========================================
    // UPDATE PAYMENT
    // ==========================================

    public function update(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin-payments');
        }


        $paymentId =
            (int)(
                $_POST['payment_id']
                ??
                $_GET['id']
                ??
                0
            );


        $status =
            trim(
                $_POST['payment_status']
                ?? ''
            );


        $transactionId =
            trim(
                $_POST['transaction_id']
                ?? ''
            );


        $validStatuses = [
            'pending',
            'paid',
            'failed'
        ];


        if (
            $paymentId <= 0
            ||
            !in_array(
                $status,
                $validStatuses,
                true
            )
        ) {

            $this->setMessage(
                'error',
                'Invalid payment information.'
            );

            $this->redirect(
                'index.php?page=admin-payments'
            );
        }


        $updated =
            $this->paymentModel
                ->updateStatus(
                    $paymentId,
                    $status
                );


        $this->setMessage(
            $updated
                ? 'success'
                : 'error',
            $updated
                ? 'Payment status updated.'
                : 'Failed to update payment.'
        );


        $this->redirect(
            'index.php?page=admin-payments'
        );
    }
}