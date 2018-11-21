<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Discount;
use App\Entity\Product;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscountController extends FOSRestController
{
    /**
     * @return View
     *
     * @Rest\Get("/all-discounts", name="all-discounts")
     */
    public function allDiscounts(): View
    {
        $allDiscounts = $this->getDoctrine()->getRepository(Discount::class)->findAll();

        return View::create($allDiscounts, Response::HTTP_OK);
    }

    /**
     * @param Discount $discount
     *
     * @return View
     *
     * @Rest\Get("/{discount}/one-discount", name="one-discount")
     */
    public function showDiscount(Discount $discount): View
    {
        return View::create($discount, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     *
     * @return View
     *
     * @Rest\Post("/create-discount", name="create-discount")
     */
    public function createDiscount(Request $request): View
    {
        $discount = new Discount();
        $discount->setDiscountType($request->get('discountType'));
        $discount->setStartTime(\DateTime::createFromFormat('j-M-Y  H:i:s', ($request->get('start_time'))));
        $discount->setStopTime(\DateTime::createFromFormat('j-M-Y  H:i:s', $request->get('stop_time')));
        $discount->setValue($request->get('value'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($discount);
        $em->flush();

        return View::create($discount, Response::HTTP_OK);
    }

    /** Postman
     * {.
    "discountType":   "fix_price",
    "start_time":     "15-Feb-2018 00:00:00",
    "stop_time":      "20-Feb-2019 00:00:00",
    "value":           2
    }
     */

    /**
     * @param Discount $discount
     *
     * @return View
     *
     * @Rest\Delete("/{discount}/remove-discount", name="remove-discount")
     */
    public function removeDiscount(Discount $discount): View
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($discount);
        $em->flush();

        return $this->allDiscounts();
    }

    /**
     * @param Request  $request
     * @param Discount $discount
     *
     * @return View
     *
     * @Rest\Post("/{discount}/add-product", name="add-product")
     */
    public function discountAddProduct(Request $request, Discount $discount): View
    {
        $products = $request->get('products');              // массив
        if (empty($products)) {
            return View::create('You choose no products', Response::HTTP_I_AM_A_TEAPOT);  // треба вивести якусь помилку
        }
        foreach ($products as $productFromRequest) {               // для кожного з продуктів
            if (null !== $productFromRequest) {                   // перевіряю чи є продукт
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($productFromRequest);
                $productDiscounts = $product->getDiscounts();    // беру всі знижки для продукта
                foreach ($productDiscounts as $productDiscount) { // перевіряю знижки по часу
                    $start = $productDiscount->getStartTime();   // старт
                    $stop = $productDiscount->getStopTime();    // стоп
                    if ($discount->getStartTime() > $stop || $discount->getStopTime() < $start) {
                    } else {
                        return View::create('Product can not have two discounts at the time', Response::HTTP_I_AM_A_TEAPOT);  // треба вивести якусь помилку
                    }
                }
                $product->addDiscount($discount);
            }
        }
        if (null !== $product) {                                   // перевіряю чи продукт існує
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        return View::create($product, Response::HTTP_OK);
    }

    /*
     * {
          "products": [2]
       }
     */

    /*
     * @param $discountEndTime
     *
     * @return View
     *
     * @Rest\Post("find-by-expiration-date", name="find-by-expiration-date")
     */
    /*
    public function findDiscountByExpirationDate(Request $request)
    {
        $discountEndTime = \DateTime::createFromFormat('j-M-Y H:i:s',($request->get('time')));
        $allDiscounts = $this->getDoctrine()
            ->getRepository(Discount::class)
            ->filterDiscountByExpirationDate($discountEndTime);

        return View::create($allDiscounts, Response::HTTP_OK);
    }
     */
}
