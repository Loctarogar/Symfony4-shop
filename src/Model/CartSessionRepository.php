<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartSessionRepository
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function get(Product $product)
    {
        return $this->session->get($product->getId());
    }

    public function set(Product $product, int $count)
    {
        $discount = $product->getDiscount();
        $this->session->set($product->getId(), ['sessionProduct' => $product, 'count' => $count, 'discount' => $discount]);
    }

    public function remove(Product $product)
    {
        $this->session->remove($product->getId());
    }

    public function getTotal()
    {
        return array_sum(array_map(
            [$this, 'getTotalCartProductPrice'],
            $this->all()
        ));
    }

    public function all()
    {
        $products = [];
        foreach ($this->session->all() as $sessionItem) {
            if (!empty($sessionItem['sessionProduct'])) {
                $products[] = $sessionItem;
            }
        }

        return $products;
    }

    private function getTotalCartProductPrice(array $cartProduct)
    {
        return $cartProduct['sessionProduct']->getPrice() * $cartProduct['count'];
    }
}
