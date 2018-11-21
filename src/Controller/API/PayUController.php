<?php declare(strict_types=1);

namespace App\Controller\API;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Order as UserOrder;
use OpenPayU_Configuration;
use OpenPayU_Order;
use OpenPayU_Util;
use OpenPayU_Exception;

class PayUController extends FOSRestController
{
    /**
     * PayUController constructor.
     *
     * @throws \OpenPayU_Exception_Configuration
     */
    public function __construct()
    {
        /* start config */
        OpenPayU_Configuration::setEnvironment('sandbox');
        //set POS ID and Second MD5 Key (from merchant admin panel)
        OpenPayU_Configuration::setMerchantPosId('344150');
        OpenPayU_Configuration::setSignatureKey('a647e96454fb9ae9048e059137ca3008');
        //set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
        OpenPayU_Configuration::setOauthClientId('344150');
        OpenPayU_Configuration::setOauthClientSecret('dab68f3ca696ca79a95b87d4312067d0');
        /* end config */
    }

    /**
     * @param UserOrder $userOrder
     *
     * @return View
     *
     * @Rest\Post("/purchase/{userOrder}", name="purchase")
     */
    public function purchase(UserOrder $userOrder)
    {
        $order = [];
        $order['notifyUrl'] = 'http://127.0.0.1:8000/api/notify-page';
        $order['continueUrl'] = 'http://127.0.0.1:8000/api/redirect-page'; // redirect to
        $order['customerIp'] = '127.0.0.1';
        $order['merchantPosId'] = OpenPayU_Configuration::getOauthClientId() ?
            OpenPayU_Configuration::getOauthClientId() :
            OpenPayU_Configuration::getMerchantPosId();
        $order['description'] = 'New order';
        $order['currencyCode'] = 'PLN';
        $order['totalAmount'] = 3200;                                            // треба передавати в integer
        $order['extOrderId'] = uniqid('', true);
        $allProducts = $userOrder->getCart()->getProductQuantities();
        $i = 0;
        foreach ($allProducts as $product) {
            $order['products'][$i]['name'] = $product->getProduct()->getTitle();
            $order['products'][$i]['unitPrice'] = '3000';                        // треба передавати integer
            $order['products'][$i]['quantity'] = $product->getQuantity();
            ++$i;
        }
        $order['buyer']['email'] = 'test_buyer_email@payu.com';
        $order['buyer']['phone'] = '123123123';
        $order['buyer']['firstName'] = 'Jan';
        $order['buyer']['lastName'] = 'Kowalski';
        $order['buyer']['language'] = 'en';
        /*~~~~~~~~ optional part DELIVERY data ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
        //Add delivery informations
        /*
        $order['buyer']['delivery']['recipientName'] = 'Robert Nowak';
        $order['buyer']['delivery']['recipientEmail'] = 'test_buyer_email@payu.com';
        $order['buyer']['delivery']['recipientPhone'] = '+48 456 123 789';
        $order['buyer']['delivery']['street'] = 'Bar St. 155';
        $order['buyer']['delivery']['postalBox'] = 'Warsaw';
        $order['buyer']['delivery']['postalCode'] = '22-222';
        $order['buyer']['delivery']['city'] = 'Warsaw';
        $order['buyer']['delivery']['state'] = 'Masovian district';
        $order['buyer']['delivery']['countryCode'] = 'PL';
        */
        try {
            $response = OpenPayU_Order::create($order);
            $status_desc = OpenPayU_Util::statusDesc($response->getStatus());
            if ('SUCCESS' === $response->getStatus()) {
                $userOrder->setPayuorderid($response->getResponse()->orderId);
                $userOrder->setIsPaid(true);
                $userOrder->setStatus($response->getStatus());
                $em = $this->getDoctrine()->getManager();
                $em->persist($userOrder);
                $em->flush();
            } else {
                echo $response->getStatus() . ': ' . $status_desc;
            }
        } catch (OpenPayU_Exception $e) {
            return View::create($e, Response::HTTP_OK);
        }
        $returnVal = $response->getResponse()->redirectUri;

        return View::create($returnVal, Response::HTTP_OK);
    }

    /**
     * @return View
     *
     * @Rest\Get("/redirect-page", name="redirect-page")
     */
    public function redirectPage(): View
    {
        $returnVal = 'Success page';

        return View::create($returnVal, Response::HTTP_OK); // з request нічого не повертається, де взяти orderId?
    }

    /**
     * @return View
     *
     * @Rest\Get("/notify-page", name="notify-page")
     */
    public function notifyPage(): View
    {
        $returnVal = 'Notifycation page';

        return View::create($returnVal, Response::HTTP_OK);
    }

    /**
     * @param UserOrder $userOrder
     *
     * @return View
     *
     * @Rest\Post("/retrieve-order/{userOrder}", name="retrieve-order")
     */
    public function retrieveOrder(UserOrder $userOrder): View
    {
        $data = [];

        try {
            $payUOrder = OpenPayU_Order::retrieve($userOrder->getPayuorderid());
            $order = $payUOrder->getResponse()->orders[0];
            foreach ($order as $key => $value) {
                $data[$key] = $value;
            }

            return View::create($data, Response::HTTP_OK);
        } catch (OpenPayU_Exception $e) {
            return View::create([
                'eM' => $e->getMessage(),
                'eC' => $e->getCode(), ], Response::HTTP_OK);
        }
    }

    /**
     * @param UserOrder $userOrder
     *
     * @return View
     *
     * @Rest\Post("retrieve-transaction/{userOrder}", name="retrieve-transaction")
     */
    public function retrieveTransaction(UserOrder $userOrder): View
    {
        $data = [];

        try {
            $payUOrder = OpenPayU_Order::retrieveTransaction($userOrder->getPayuorderid());
            $order = $payUOrder->getResponse()->transactions[0]->paymentFlow;
            $data['paymentFlow'] = $order;
            $order = $payUOrder->getResponse()->transactions[0]->card->cardData;
            foreach ($order as $key => $value) {
                $data[$key] = $value;
            }

            return View::create($data, Response::HTTP_OK);
        } catch (OpenPayU_Exception $e) {
            return View::create([
                'eM' => $e->getMessage(),
                'eC' => $e->getCode(), ], Response::HTTP_OK);
        }
    }

    /**
     * @param UserOrder $userOrder
     *
     * @return View
     *
     * @Rest\Delete("/cancel-order/{userOrder}", name="cancel-order")
     */
    public function cancelOrder(UserOrder $userOrder): View
    {
        //https://github.com/PayU/openpayu_php/blob/master/examples/v2/order/OrderCancel.php // не працює
        try {
            $response = OpenPayU_Order::cancel($userOrder->getPayuorderid());

            return View::create([
                'status' => $response->getStatus(),
                'message' => $response->getMessage(),
                'error' => $response->getError(),
                'success' => $response->getSuccess(),
            ], Response::HTTP_OK);
        } catch (OpenPayU_Exception $e) {
            return View::create([
                'eM' => $e->getMessage(),
                'eC' => $e->getCode(), ], Response::HTTP_OK);
        }
    }
}
