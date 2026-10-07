<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../Model/Farmer.php';
require_once __DIR__ . '/../Model/Crop.php';
require_once __DIR__ . '/../Model/Order.php';


class FarmerController extends BaseController
{
    // Single source of truth for allowed values.
    // The views (add/edit/search) read the same lists.

    public const CATEGORIES = [
        'vegetable' => 'Vegetable',
        'fruit'     => 'Fruit',
        'grain'     => 'Grain / Rice',
        'spice'     => 'Spice',
        'other'     => 'Other',
    ];

    public const UNITS = ['kg', 'piece'];

    // "available" is what Crop::getActive()/search() and the
    // cart look for. Anything else hides the crop from shoppers.
    public const STATUSES = ['available', 'unavailable'];


    private Farmer $farmerModel;
    private Crop $cropModel;
    private Order $orderModel;


    public function __construct()
    {
        $this->farmerModel = new Farmer();
        $this->cropModel = new Crop();
        $this->orderModel = new Order();
    }


    // ==========================================
    // HELPERS
    // ==========================================

    /**
     * Logged-in farmer's profile row.
     * If the profile is missing we log the user out instead of
     * redirecting to login (login would bounce a logged-in
     * farmer straight back here = redirect loop).
     */
    private function currentFarmer(): array
    {
        $this->requireRole('farmer');

        $farmer = $this->farmerModel
            ->findByUserId($this->userId());

        if (!$farmer) {
            $this->redirect('index.php?page=logout');
        }

        return $farmer;
    }


    private function requirePost(string $fallback): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($fallback);
        }
    }


    /**
     * Read + validate the crop form.
     * Returns [data, error].
     */
    private function readCropForm(): array
    {
        $data = [
            'crop_name'   => trim($_POST['crop_name'] ?? ''),
            'category'    => strtolower(trim($_POST['category'] ?? '')),
            'description' => trim($_POST['description'] ?? ''),
            'price'       => (float)($_POST['price_per_kg'] ?? 0),
            'quantity'    => (float)($_POST['quantity'] ?? 0),
            'unit'        => strtolower(trim($_POST['unit'] ?? 'kg')),
            'status'      => strtolower(trim($_POST['status'] ?? 'available')),
        ];

        if ($data['crop_name'] === '' || strlen($data['crop_name']) > 100) {
            return [$data, 'Please enter a valid crop name.'];
        }

        if (!array_key_exists($data['category'], self::CATEGORIES)) {
            return [$data, 'Please select a valid category.'];
        }

        if ($data['price'] <= 0) {
            return [$data, 'Price must be greater than zero.'];
        }

        if ($data['quantity'] < 0) {
            return [$data, 'Quantity cannot be negative.'];
        }

        if (!in_array($data['unit'], self::UNITS, true)) {
            return [$data, 'Please select a valid unit.'];
        }

        if (!in_array($data['status'], self::STATUSES, true)) {
            return [$data, 'Invalid crop status.'];
        }

        return [$data, null];
    }


    /**
     * Crop that belongs to the logged-in farmer, or redirect.
     */
    private function ownCropOrRedirect(int $cropId, array $farmer): array
    {
        $crop = $this->cropModel->findById($cropId);

        if (!$crop) {
            $this->setMessage('error', 'Crop not found.');
            $this->redirect('index.php?page=farmer-crops');
        }

        if ((int)$crop['farmer_id'] !== (int)$farmer['farmer_id']) {
            $this->redirect('index.php?page=unauthorized');
        }

        return $crop;
    }


    // ==========================================
    // BECOME FARMER
    // ==========================================

    public function becomeFarmer(): void
    {
        if (isset($_SESSION['user_id'])) {

            if ($this->userRole() === 'farmer') {
                $this->redirect('index.php?page=farmer-dashboard');
            }

            $this->setMessage(
                'error',
                'Please log out first to register a farmer account.'
            );

            $this->redirect('index.php');
        }

        $this->redirect('index.php?page=farmer-register');
    }


    // ==========================================
    // DASHBOARD
    // ==========================================

    public function dashboard(): void
    {
        $farmer = $this->currentFarmer();

        $crops = $this->cropModel
            ->getByFarmer((int)$farmer['farmer_id']);

        $available = 0;
        $totalQuantity = 0.0;

        foreach ($crops as $crop) {

            $totalQuantity += (float)$crop['quantity'];

            if (strtolower($crop['status']) === 'available') {
                $available++;
            }
        }

        $this->view(
            'farmer/dashboard',
            [
                'farmer'           => $farmer,
                'crops'            => $crops,
                'totalCrops'       => count($crops),
                'activeCrops'      => $available,
                'unavailableCrops' => count($crops) - $available,
                'totalQuantity'    => $totalQuantity,
            ]
        );
    }


    // ==========================================
    // CROPS
    // ==========================================

    public function crops(): void
    {
        $farmer = $this->currentFarmer();

        $this->view(
            'farmer/crops',
            [
                'farmer' => $farmer,
                'crops'  => $this->cropModel
                    ->getByFarmer((int)$farmer['farmer_id']),
            ]
        );
    }


    public function addCrop(): void
    {
        $this->currentFarmer();

        $this->view(
            'farmer/add_crop',
            [
                'category' => '',
            ]
        );
    }


    public function createCrop(): void
    {
        $farmer = $this->currentFarmer();

        $this->requirePost('index.php?page=farmer-add-crop');

        [$data, $error] = $this->readCropForm();

        if ($error !== null) {
            $this->setMessage('error', $error);
            $this->redirect('index.php?page=farmer-add-crop');
        }

        $created = $this->cropModel->create(
            (int)$farmer['farmer_id'],
            $data['crop_name'],
            $data['category'],
            $data['description'],
            $data['price'],
            $data['quantity'],
            $data['unit'],
            'available'
        );

        $this->setMessage(
            $created ? 'success' : 'error',
            $created
                ? 'Crop added successfully.'
                : 'Failed to add crop.'
        );

        $this->redirect('index.php?page=farmer-crops');
    }


    public function editCrop(): void
    {
        $farmer = $this->currentFarmer();

        $crop = $this->ownCropOrRedirect(
            (int)($_GET['id'] ?? 0),
            $farmer
        );

        $this->view('farmer/edit_crop', ['crop' => $crop]);
    }


    public function updateCrop(): void
    {
        $farmer = $this->currentFarmer();

        $this->requirePost('index.php?page=farmer-crops');

        $cropId = (int)($_POST['crop_id'] ?? 0);

        $this->ownCropOrRedirect($cropId, $farmer);

        [$data, $error] = $this->readCropForm();

        if ($error !== null) {
            $this->setMessage('error', $error);
            $this->redirect(
                'index.php?page=farmer-edit-crop&id=' . $cropId
            );
        }

        $updated = $this->cropModel->update(
            $cropId,
            $data['crop_name'],
            $data['category'],
            $data['description'],
            $data['price'],
            $data['quantity'],
            $data['unit'],
            $data['status']
        );

        $this->setMessage(
            $updated ? 'success' : 'error',
            $updated
                ? 'Crop updated successfully.'
                : 'Failed to update crop.'
        );

        $this->redirect('index.php?page=farmer-crops');
    }


    public function deleteCrop(): void
    {
        $farmer = $this->currentFarmer();

        // State-changing action: POST only (a GET link can be
        // triggered by any page/image/prefetch).
        $this->requirePost('index.php?page=farmer-crops');

        $cropId = (int)($_POST['crop_id'] ?? 0);

        $this->ownCropOrRedirect($cropId, $farmer);

        $deleted = $this->cropModel->delete($cropId);

        $this->setMessage(
            $deleted ? 'success' : 'error',
            $deleted
                ? 'Crop deleted successfully.'
                : 'Failed to delete crop. '
                  . 'It may be part of an existing order.'
        );

        $this->redirect('index.php?page=farmer-crops');
    }


    // ==========================================
    // INVENTORY
    // ==========================================

    public function inventory(): void
    {
        $farmer = $this->currentFarmer();

        $this->view(
            'farmer/inventory',
            [
                'crops' => $this->cropModel
                    ->getByFarmer((int)$farmer['farmer_id']),
            ]
        );
    }


    // ==========================================
    // SELL / SALES
    // "Selling" = listing a crop; "sales" = orders received.
    // (The old methods rendered views that did not exist.)
    // ==========================================

    public function sell(): void
    {
        $this->currentFarmer();

        $this->redirect('index.php?page=farmer-add-crop');
    }


    public function sales(): void
    {
        $this->currentFarmer();

        $this->redirect('index.php?page=farmer-orders');
    }


    // ==========================================
    // ORDERS
    // ==========================================

    public function orders(): void
    {
        $farmer = $this->currentFarmer();

        $this->view(
            'farmer/orders',
            [
                'orders' => $this->orderModel
                    ->getByFarmer((int)$farmer['farmer_id']),
            ]
        );
    }


    // ==========================================
    // PROFILE
    // ==========================================

    public function profile(): void
    {
        $this->view(
            'farmer/profile',
            [
                'farmer' => $this->currentFarmer(),
            ]
        );
    }
}
