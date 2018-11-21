<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Order;
use App\Repository\CartRepository;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Services\CartService;

class OrderController extends FOSRestController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Show Order.
     *
     * @param Order $order
     *
     * @Rest\Get("order/{order}", name="order-show")
     *
     * @return View
     */
    public function showOrder(Order $order): View
    {
        return View::create($order, Response::HTTP_OK);  // todo: чомусь показує "видалені" Замовлення
    }

    /**
     * @param CartRepository $cartRepository
     *
     * @return View
     *
     * @Rest\Post("order/create")
     */
    public function createOrder(CartRepository $cartRepository)
    {
        $user = $this->getUser();                          // знаходимо користувача,
        $cart = $cartRepository->findByOwner($user);       // знаходжу корзину по користувачу
        $em = $this->getDoctrine()->getManager();
        if (null === $cart) {                              // якщо нема корзини в базі
            $cart = $this->cartService->getCartSession();  // отримую корзину із сессії
            $cart->setOwner($user);
            $em->persist($cart);
            $em->flush();
        }
        $order = new Order();                              // новий об"єкт Замовлення
        $order->setCart($cart);                            // додаю в замовлення корзину
        $order->setUser($user);                            //                    користувача
        $em->persist($order);
        $cart->setIsClosed(true);                  // закриваю корзину
        $em->persist($cart);
        $em->flush();

        return View::create($order, Response::HTTP_CREATED);
    }

    /**
     * Remove Order.
     *
     * @param OrderRepository $orderRepository
     * @param Order           $order
     *
     * @Rest\Delete("order/{id}/remove", name="order-remove")
     *
     * @return View
     */
    public function removeOrder(OrderRepository $orderRepository, Order $order): View
    {
        $data = $orderRepository->find($order);
        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        return View::create($data, Response::HTTP_OK);
    }
}
