<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Delivery.php';
require_once __DIR__ . '/../Model/DeliveryMan.php';


class DeliveryController extends BaseController
{
    private Delivery $deliveryModel;
    private DeliveryMan $deliveryManModel;


    public function __construct()
    {
        $this->deliveryModel =
            new Delivery();

        $this->deliveryManModel =
            new DeliveryMan();
    }


    // ==========================================
    // ADMIN DELIVERY MANAGEMENT
    // ==========================================

    public function index(): void
    {
        $this->requireRole('admin');


        $deliveries =
            $this->deliveryModel
                ->getAll();


        $deliveryMen =
            $this->deliveryManModel
                ->getAll();


        $this->view(
            'admin/deliveries',
            [
                'deliveries' =>
                    $deliveries,

                'deliveryMen' =>
                    $deliveryMen
            ]
        );
    }


    // ==========================================
    // ASSIGN DELIVERY MAN
    // ==========================================

    public function assign(): void
    {
        $this->requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('index.php?page=admin-deliveries');
        }


        $deliveryId =
            (int)(
                $_POST['delivery_id']
                ?? 0
            );


        $deliveryManId =
            (int)(
                $_POST['delivery_man_id']
                ?? 0
            );


        if (
            $deliveryId <= 0
            ||
            $deliveryManId <= 0
        ) {

            $this->setMessage(
                'error',
                'Invalid delivery information.'
            );

            $this->redirect(
                'index.php?page=admin-deliveries'
            );
        }


        $assigned =
            $this->deliveryModel
                ->assign(
                    $deliveryId,
                    $deliveryManId
                );


        $this->setMessage(
            $assigned
                ? 'success'
                : 'error',
            $assigned
                ? 'Delivery assigned successfully.'
                : 'Failed to assign delivery.'
        );


        $this->redirect(
            'index.php?page=admin-deliveries'
        );
    }


    // ==========================================
    // UPDATE DELIVERY
    // ==========================================

    public function update(): void
    {
        $this->requireRole(
            ['admin', 'delivery_man', 'delivery']
        );


        $deliveryId =
            (int)(
                $_POST['delivery_id']
                ??
                $_GET['id']
                ??
                0
            );


        $status =
            trim(
                $_POST['delivery_status']
                ?? ''
            );


        $validStatuses = [

            'assigned',

            'picked_up',

            'out_for_delivery',

            'delivered'
        ];


        if (
            $deliveryId <= 0
            ||
            !in_array(
                $status,
                $validStatuses,
                true
            )
        ) {

            $this->setMessage(
                'error',
                'Invalid delivery information.'
            );

            $this->redirect(
                'index.php?page=delivery-dashboard'
            );
        }


        /*
         * Delivery Man should only
         * update his own delivery.
         */

        if (
            $this->userRole()
            !==
            'admin'
        ) {

            $deliveryMan =
                $this->deliveryManModel
                    ->findByUserId(
                        $this->userId()
                    );


            $delivery =
                $this->deliveryModel
                    ->findById(
                        $deliveryId
                    );


            if (
                !$delivery
                ||
                !$deliveryMan
                ||
                (int)$delivery[
                    'delivery_man_id'
                ]
                !==
                (int)$deliveryMan[
                    'delivery_man_id'
                ]
            ) {

                $this->redirect(
                    'index.php?page=unauthorized'
                );
            }
        }


        $updated =
            $this->deliveryModel
                ->updateStatus(
                    $deliveryId,
                    $status
                );


        $this->setMessage(
            $updated
                ? 'success'
                : 'error',
            $updated
                ? 'Delivery status updated.'
                : 'Failed to update delivery status.'
        );


        if (
            $this->userRole()
            ===
            'admin'
        ) {

            $this->redirect(
                'index.php?page=admin-deliveries'
            );
        }


        $this->redirect(
            'index.php?page=delivery-pending'
        );
    }


    // ==========================================
    // DELIVERY DASHBOARD
    // ==========================================

    public function dashboard(): void
    {
        $this->requireRole(
            ['delivery_man', 'delivery']
        );


        $deliveryMan =
            $this->deliveryManModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$deliveryMan) {

            $this->setMessage(
                'error',
                'Delivery profile not found.'
            );

            $this->redirect(
                'index.php?page=login'
            );
        }


        $deliveries =
            $this->deliveryModel
                ->getByDeliveryMan(
                    $deliveryMan[
                        'delivery_man_id'
                    ]
                );


        $total = count(
            $deliveries
        );

        $pending = 0;
        $completed = 0;


        foreach ($deliveries as $delivery) {

            if (
                $delivery[
                    'delivery_status'
                ]
                ===
                'delivered'
            ) {

                $completed++;

            } else {

                $pending++;
            }
        }


        $this->view(
            'delivery/dashboard',
            [
                'deliveryMan' =>
                    $deliveryMan,

                'deliveries' =>
                    $deliveries,

                'total' =>
                    $total,

                'pending' =>
                    $pending,

                'completed' =>
                    $completed
            ]
        );
    }


    // ==========================================
    // PENDING DELIVERIES
    // ==========================================

    public function pending(): void
    {
        $this->requireRole(
            ['delivery_man', 'delivery']
        );


        $deliveryMan =
            $this->deliveryManModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$deliveryMan) {
            $this->redirect('index.php?page=logout');
        }

        $deliveries =
            $this->deliveryModel
                ->getByDeliveryMan(
                    $deliveryMan[
                        'delivery_man_id'
                    ]
                );


        $pendingDeliveries =
            array_filter(
                $deliveries,
                function ($delivery) {

                    return $delivery[
                        'delivery_status'
                    ] !== 'delivered';
                }
            );


        $this->view(
            'delivery/pending_deliveries',
            [
                'deliveries' =>
                    $pendingDeliveries
            ]
        );
    }


    // ==========================================
    // DELIVERY HISTORY
    // ==========================================

    public function history(): void
    {
        $this->requireRole(
            ['delivery_man', 'delivery']
        );


        $deliveryMan =
            $this->deliveryManModel
                ->findByUserId(
                    $this->userId()
                );


        if (!$deliveryMan) {
            $this->redirect('index.php?page=logout');
        }

        $deliveries =
            $this->deliveryModel
                ->getByDeliveryMan(
                    $deliveryMan[
                        'delivery_man_id'
                    ]
                );


        $history =
            array_filter(
                $deliveries,
                function ($delivery) {

                    return $delivery[
                        'delivery_status'
                    ] === 'delivered';
                }
            );


        $this->view(
            'delivery/history',
            [
                'deliveries' =>
                    $history
            ]
        );
    }


    // ==========================================
    // UPDATE PAGE
    // ==========================================

    public function edit(): void
    {
        $this->requireRole(
            ['admin', 'delivery_man', 'delivery']
        );


        $deliveryId =
            (int)(
                $_GET['id'] ?? 0
            );


        $delivery =
            $this->deliveryModel
                ->findById(
                    $deliveryId
                );


        if (!$delivery) {

            $this->setMessage(
                'error',
                'Delivery not found.'
            );

            $this->redirect(
                'index.php?page=delivery-pending'
            );
        }


        $this->view(
            'delivery/update_delivery',
            [
                'delivery' =>
                    $delivery
            ]
        );
    }
}