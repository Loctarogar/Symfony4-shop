<?php declare(strict_types=1);

namespace App\Model;

use App\Entity\Product;

class CartModel
{
    private $cartSessionRepository;

    public function __construct(CartSessionRepository $cartSessionRepository)
    {
        $this->cartSessionRepository = $cartSessionRepository;
    }

    public function addProduct(Product $product)
    {
        $productInCart = $this->cartSessionRepository->get($product);
        $count = $productInCart['count'] ?? 0;
        ++$count;
        $this->cartSessionRepository->set($product, $count);
    }

    public function removeProduct(Product $product)
    {
        $productInCart = $this->cartSessionRepository->get($product);
        $count = $productInCart['count'] ?? 0;
        if (--$count <= 0) {
            $this->cartSessionRepository->remove($product);

            return;
        }
        $this->cartSessionRepository->set($product, $count);
    }

    public function getTotal()
    {
        return $this->cartSessionRepository->getTotal();
    }

    public function getProducts()
    {
        return $this->cartSessionRepository->all();
    }
}
