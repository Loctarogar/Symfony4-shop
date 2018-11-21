<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\Cart;
use App\Entity\Discount;
use App\Model\CartModel;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\ProductQuantity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CartService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var User|string
     */
    private $cart_user;

    /**
     * @var CartModel
     */
    private $cart_session;

    /**
     * @param EntityManagerInterface $entityManager
     * @param Security               $security
     * @param CartModel              $cartModel
     */
    public function __construct(EntityManagerInterface $entityManager, Security $security, CartModel $cartModel)
    {
        $this->entityManager = $entityManager;
        $this->cart_user = $security->getUser();
        $this->cart_session = $cartModel;
    }

    /**
     * @param Product $product
     *
     * @return $this
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function addProduct(Product $product)
    {
        if ($this->isAuth()) {
            $this->addProductDatabase($product);
        } else {
            $this->addProductSession($product);
        }

        return $this;
    }

    /**
     * @param Product $product
     *
     * @return $this
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function removeProduct(Product $product)
    {
        if ($this->isAuth()) {
            $this->removeProductDatabase($product);
        } else {
            $this->removeProductSession($product);
        }

        return $this;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->isAuth() ? $this->getCartDatabase() : $this->getCartSession();
    }

    /**
     * @return bool
     */
    private function isAuth(): bool
    {
        if ($this->cart_user instanceof User) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     *
     * @return Cart
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function addProductDatabase(Product $product)
    {
        $cart = $this->getCartDatabase();
        $productQuantity = $this->entityManager
            ->getRepository(ProductQuantity::class)
            ->getProductFromProductQuantity($cart, $product);
        if (null === $productQuantity) {                       // перевряю чи є продукт в корзині(в $productQuantity)
            $productQuantity = new ProductQuantity();
            $productQuantity->setCart($cart);
            $productQuantity->setQuantity(1);
            $productQuantity->setProduct($product);
        } else {
            $quantity = $productQuantity->getQuantity();
            $productQuantity->setQuantity($quantity + 1);
        }
        $discount = $product->getDiscount();
        if (null !== $discount) {
            $productQuantity->setDiscount($discount);
        }
        $this->entityManager->persist($productQuantity);
        $this->entityManager->flush();

        return $cart;
    }

    /**
     * @param Product $product
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function removeProductDatabase(Product $product)
    {
        $cart = $this->getCartDatabase();
        $productQuantityRepository = $this->entityManager
            ->getRepository(ProductQuantity::class);
        $productQuantity = $productQuantityRepository
            ->getProductFromProductQuantity($cart, $product);
        if (null !== $productQuantity) {
            $this->entityManager->remove($productQuantity);
            $this->entityManager->flush();
        }
    }

    /**
     * @param Product $product
     */
    private function addProductSession(Product $product)
    {
        $this->cart_session->addProduct($product);
    }

    private function removeProductSession(Product $product)
    {
        $this->cart_session->removeProduct($product);
    }

    /**
     * @return Cart|null
     */
    private function getCartDatabase()
    {
        $cartRepository = $this->entityManager
            ->getRepository(Cart::class);
        $user = $this->cart_user;
        $cart = $cartRepository->findByOwner($user);
        if (null === $cart) {
            $cart = new Cart();
            $cart->setOwner($user);
            $this->entityManager->persist($cart);
            $this->entityManager->flush();
        }

        return $cart;
    }

    /**
     * @return Cart
     */
    public function getCartSession()
    {
        $sessionCart = $this->cart_session->getProducts();
        $cart = new Cart();
        foreach ($sessionCart as $prod) {
            $product = $this->entityManager
                ->getRepository(Product::class)
                ->find($prod['sessionProduct']);
            $discount = $product->getDiscount();
            $productQuantity = new ProductQuantity();
            $productQuantity->setProduct($product);
            $productQuantity->setQuantity($prod['count']);
            if (null !== $discount) {
                $productQuantity->setDiscount($discount);
            }
            $cart->addProductQuantity($productQuantity);
        }

        return $cart;
    }

    /**
     * @return float|int
     */
    public function getTotalSum()
    {
        $cart = $this->getCart();
        $totalPrice = 0;
        foreach ($cart->getProductQuantities() as $productQuantity) {
            $product = $productQuantity->getProduct();
            $productPrice = $product->getPrice();
            if (null !== $productQuantity->getDiscount()) {
                $discount = $productQuantity->getDiscount()->getValue();
                if (Discount::FIX_PRICE === $productQuantity->getDiscount()->getDiscountType()) {
                    $productPrice = $product->getPrice() - $discount;
                }
                if (Discount::PERCENT === $productQuantity->getDiscount()->getDiscountType()) {
                    $productPrice = ($product->getPrice() / 100) * (100 - $discount);
                }
            }
            $price = $productQuantity->getQuantity() * $productPrice;
            $totalPrice += $price;
        }

        return $totalPrice;
    }
}
