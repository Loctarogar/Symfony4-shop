<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Services\CartService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;

class CartController extends FOSRestController
{
    /**
     * @param CartService $cartService
     * @param Product     $product
     *
     * @return View
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @Rest\Post("/cart/{product}/add", name="cart-add")
     */
    public function addProduct(CartService $cartService, Product $product): View
    {
        $cartService->addProduct($product);

        //return View::create($a, Response::HTTP_CREATED);
        return View::create([
            'cart' => $cartService->getCart(),
            'sum' => $cartService->getTotalSum(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Product     $product
     * @param CartService $cartService
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return View
     * @Rest\Delete("cart/{product}/remove", name="cart-remove")
     */
    public function removeProduct(Product $product, CartService $cartService): View
    {
        $cartService->removeProduct($product);

        return View::create([
            'cart' => $cartService->getCart(),
            'sum' => $cartService->getTotalSum(),
        ], Response::HTTP_OK);
    }

    /**
     * @param CartService $cartService
     *
     * @return View
     *
     * @Rest\Get("cart-get", name="cart-get")
     */
    public function cart(CartService $cartService)
    {
        return View::create([
            'cart' => $cartService->getCart(),
            'sum' => $cartService->getTotalSum(),
        ]);
    }
}
