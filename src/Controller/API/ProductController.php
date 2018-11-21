<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Tag;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class ProductController extends FOSRestController
{
    /**
     * Retrieves a collection of Product resource.
     *
     * @param Request $request
     *
     * @return View
     *
     * @Rest\Post("/product", name="product_controller")
     */
    public function index(Request $request): View
    {
        $tags = $request->get('tags', []);
        $category = $request->get('category');
        $minPrice = $request->get('minPrice');
        $maxPrice = $request->get('maxPrice');
        $order = $request->get('order');
        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findByFilters($category, $minPrice, $maxPrice, $order, $tags);

        return View::create($products, Response::HTTP_OK);
    }

    /** для Postman
     * {
     *   "tags": [1, 2],
     *   "maxPrice": 15,
     *   "order": "DESC"
     * }.
     */

    /**
     * Creates a Product.
     *
     * @Rest\Post("/product/add")
     *
     * @param Request $request
     *
     * @return View
     */
    public function addProduct(Request $request): View
    {
        $data = new Product();
        $data->setTitle($request->get('title'));
        $data->setPrice($request->get('price'));
        $data->setDescription($request->get('description'));

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($request->get('category'));
        $data->setCategory($category);

        $tag = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->find($request->get('tag'));

        $data->addTag($tag);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();

        return View::create($data, Response::HTTP_CREATED);
    }

    /*Postman data
{
"title": "product_1",
"price": 12,
"description": "description for product",
"category": 5,
"tag": {"id": 3}
}
*/

    /**
     * Retrieves single Product.
     *
     * @Rest\Get("/product/{id}")
     */
    public function show(Product $product): View
    {
        return View::create($product, Response::HTTP_OK);
    }

    /**
     * Replaces Category resource.
     *
     * @Rest\Put("/product/{id}")
     */
    public function editProduct(Request $request, Product $product): View
    {
        if (null !== $product) {
            $product->setTitle($request->get('title'));
            $product->setPrice($request->get('price'));
            $product->setDescription($request->get('description'));

            $category = $this->getDoctrine()
                ->getRepository(Category::class)
                ->find($request->get('category'));
            $product->setCategory($category);  //Product::setCategory() must be an instance of App\\Entity\\Category

            $tag = $this->getDoctrine()
                ->getRepository(Tag::class)
                ->find($request->get('tag'));
            $product->addTag($tag);   //Product::addTag() must be an instance of App\\Entity\\Tag

            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }

        return View::create($product, Response::HTTP_OK);
    }

    /**
     * @Rest\Delete("/product/{id}/delete")
     * @Security("is_granted('delete', product)")
     */
    public function delete(Product $product, ProductRepository $productRepository): View
    {
        $data = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($product);

        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * Replaces Category resource.
     *
     * @Rest\Post("/product/{id}/change-category")
     */
    public function changeCategory(Request $request, Product $product): View
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($request->request->get('category'));
        $product->setCategory($category);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return View::create($product, Response::HTTP_OK);
    }

    /*
     * Postman data
      {
         "tag": {"id": 7}
      }
     */

    /**
     * Add Tag resource.
     *
     * @Rest\Post("/{id}/add-tag")
     */
    public function addTag(Request $request, Product $product): View
    {
        $tag = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->find($request->request->get('tag'));
        $product->addTag($tag);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return View::create($product, Response::HTTP_OK);
    }

    /* Postman data
      {
         "tag": {"id": 7}
      }
     */

    /**
     * Add Tag resource.
     *
     * @Rest\Post("/{id}/remove-tag")
     */
    public function removeTag(Request $request, Product $product): View
    {
        $tag = $this->getDoctrine()
            ->getRepository(Tag::class)
            ->find($request->request->get('tag'));
        $product->removeTag($tag);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($product);
        $entityManager->flush();

        return View::create($product, Response::HTTP_OK);
    }
}
