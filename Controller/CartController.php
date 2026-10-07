<?php

require_once __DIR__ . '/BaseController.php';

require_once __DIR__ . '/../Model/Customer.php';
require_once __DIR__ . '/../Model/Cart.php';
require_once __DIR__ . '/../Model/Crop.php';


class CartController extends BaseController
{
    private Customer $customerModel;
    private Cart $cartModel;
    private Crop $cropModel;


    public function __construct()
    {
        $this->customerModel = new Customer();
        $this->cartModel = new Cart();
        $this->cropModel = new Crop();
    }


    /**
     * Logged-in customer's profile row.
     * Missing profile -> log out (redirecting to login would
     * bounce a logged-in customer back = redirect loop).
     */
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


    private function requirePost(string $fallback): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($fallback);
        }
    }


    // ==========================================
    // CART PAGE
    // ==========================================

    public function index(): void
    {
        $customer = $this->currentCustomer();

        $items = $this->cartModel
            ->getByCustomer((int)$customer['customer_id']);

        $total = 0.0;

        foreach ($items as &$item) {

            $item['subtotal'] =
                (float)$item['cart_quantity']
                * (float)$item['price_per_kg'];

            $total += $item['subtotal'];
        }

        unset($item);

        $this->view(
            'customer/cart',
            [
                'customer' => $customer,
                'items'    => $items,
                'total'    => $total,
            ]
        );
    }


    // ==========================================
    // ADD
    // ==========================================

    public function add(): void
    {
        $customer = $this->currentCustomer();

        $this->requirePost('index.php?page=crops');

        $cropId = (int)($_POST['crop_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($cropId <= 0 || $quantity <= 0) {

            $this->setMessage('error', 'Invalid cart information.');

            $this->redirect('index.php?page=crops');
        }

        $crop = $this->cropModel->findById($cropId);

        // Was compared with 'active', a status that is never
        // stored (crops are 'available'), so nothing could ever
        // be added to a cart.

        if (
            !$crop
            || $crop['status'] !== 'available'
            || (float)$crop['quantity'] <= 0
        ) {

            $this->setMessage(
                'error',
                'This product is not available.'
            );

            $this->redirect('index.php?page=crops');
        }

        // Stock check must include what is already in the cart.

        $existing = $this->cartModel->findItem(
            (int)$customer['customer_id'],
            $cropId
        );

        $alreadyInCart = $existing ? (int)$existing['quantity'] : 0;

        if ($quantity + $alreadyInCart > (float)$crop['quantity']) {

            $this->setMessage(
                'error',
                'Requested quantity is not available.'
            );

            $this->redirect(
                'index.php?page=product-details&id=' . $cropId
            );
        }

        $added = $this->cartModel->add(
            (int)$customer['customer_id'],
            $cropId,
            $quantity
        );

        $this->setMessage(
            $added ? 'success' : 'error',
            $added
                ? 'Product added to cart.'
                : 'Failed to add product to cart.'
        );

        $this->redirect('index.php?page=cart');
    }


    // ==========================================
    // UPDATE
    // ==========================================

    public function update(): void
    {
        $customer = $this->currentCustomer();

        $this->requirePost('index.php?page=cart');

        $cartId = (int)($_POST['cart_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);

        if ($cartId <= 0) {

            $this->setMessage('error', 'Invalid cart item.');

            $this->redirect('index.php?page=cart');
        }

        // Only touch the customer's own rows, and respect stock.

        foreach (
            $this->cartModel->getByCustomer(
                (int)$customer['customer_id']
            ) as $row
        ) {
            if (
                (int)$row['cart_id'] === $cartId
                && $quantity > (float)$row['available_quantity']
            ) {

                $this->setMessage(
                    'error',
                    'Requested quantity is not available.'
                );

                $this->redirect('index.php?page=cart');
            }
        }

        $updated = $this->cartModel->updateQuantity(
            $cartId,
            $quantity,
            (int)$customer['customer_id']
        );

        $this->setMessage(
            $updated ? 'success' : 'error',
            $updated ? 'Cart updated.' : 'Failed to update cart.'
        );

        $this->redirect('index.php?page=cart');
    }


    // ==========================================
    // REMOVE
    // ==========================================

    public function remove(): void
    {
        $customer = $this->currentCustomer();

        $this->requirePost('index.php?page=cart');

        $cartId = (int)($_POST['cart_id'] ?? 0);

        // Before: any logged-in customer could delete any other
        // customer's cart row just by changing ?id=.

        $removed = $this->cartModel->remove(
            $cartId,
            (int)$customer['customer_id']
        );

        $this->setMessage(
            $removed ? 'success' : 'error',
            $removed
                ? 'Item removed from cart.'
                : 'Failed to remove item.'
        );

        $this->redirect('index.php?page=cart');
    }


    // ==========================================
    // CLEAR
    // ==========================================

    public function clear(): void
    {
        $customer = $this->currentCustomer();

        $this->requirePost('index.php?page=cart');

        $cleared = $this->cartModel
            ->clear((int)$customer['customer_id']);

        $this->setMessage(
            $cleared ? 'success' : 'error',
            $cleared ? 'Cart cleared.' : 'Failed to clear cart.'
        );

        $this->redirect('index.php?page=cart');
    }
}
