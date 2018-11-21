<?php declare(strict_types=1);

namespace App\Controller\API;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Services\CartService;
use App\Entity\Payment;
use App\Entity\Order;
use App\Entity\User;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Payum;

class PaymentController extends FOSRestController
{
    /**
     * @return Payum
     */
    protected function getPayum()
    {
        return $this->get('payum');
    }

    /**
     * @param Order       $order
     * @param CartService $cartService
     *
     * @return mixed
     *
     * @Rest\Post("/prepare-action/{order}", name="prepare-action")
     */
    public function prepareAction(Order $order, CartService $cartService, Request $request): View
    {
        if (true === $order->getIsPaid()) {
            throw new BadRequestHttpException('Order already payed.');
        }
        /* @var $user User */
        $user = $this->getUser();
        $gatewayName = 'payu';
        $storage = $this->get('payum')->getStorage('App\Entity\Payment');
        //$storage = $this->get('payum')->getStorage(Payment::class);
        /* @var $payment Payment */
        $payment = $storage->create();
        $total = $cartService->getTotalSum();
        $payment->setUserOrder($order);
        $payment->setCurrencyCode('PLN');
        $payment->setTotalAmount($total);
        $payment->setDescription('Shop payment.');
        $payment->setClientId(md5((string) $user->getId()));
        $payment->setClientEmail($user->getEmail());
        $details = [
            'invoiceDisabled' => true, // use this only when you want to hide invoice checkbox on payment form
        ];
        if (null !== $userInfo = $user->getUsername()) {
            $details = [
                'firstName' => $user->getUsername(),
            ];
        }
        $payment->setDetails($details);
        $storage->update($payment);
        $captureToken = $this->getPayum()
            ->getTokenFactory()
            ->createCaptureToken(
                $gatewayName,
                $payment,
                'payum_capture_do');

        return View::create($captureToken->getTargetUrl(), Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return mixed
     *
     * @throws \Exception
     *
     * @Rest\Get("done", name="payum_capture_do")
     */
    public function doneAction(Request $request)
    {
        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $payment = $this->getPayum()->getGateway($token->getGatewayName());
        $payment->execute($status = new GetHumanStatus($token));
        $paymentId = $status->getToken()->getDetails()->getId();
        $paymentClass = $status->getToken()->getDetails()->getClass();
        $storage = $this->getPayum()->getStorage($paymentClass);

        /* @var $payment Payment */
        $payment = $storage->find($paymentId);
        if (!$payment) {
            throw new NotFoundHttpException('Payment not found');
        }
        $order = $payment->getUserOrder();
        if ($status->isCaptured()) {
            // payment succeeded
            $order->setIsPaid(true);
            $order->setStatus(Order::STATUS_ACCEPTED);
        } elseif ($status->isPending()) {
            // payment pending
        } else {
            // payment failed
            $order->setIsPaid(false);
            $order->setStatus(Order::STATUS_CANCELLED);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        return View::create($payment, Response::HTTP_OK);
    }
}
