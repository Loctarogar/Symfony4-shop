<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class CategoryController extends FOSRestController
{
    /**
     * Retrieves a collection of Category resource.
     *
     * @Rest\View(serializerGroups={"details"})
     * @Rest\Get("/category")
     */
    public function index(CategoryRepository $categoryRepository): View
    {
        $data = $categoryRepository->findAll();

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * Creates a Category.
     *
     * @Rest\Post("/category/add")
     *
     * @param Request $request
     *
     * @return View
     */
    public function addCategory(Request $request): View
    {
        $category = new Category();
        $category->setName($request->get('name'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

        return View::create($category, Response::HTTP_CREATED);
    }

    /**
     * Retrieves single Category.
     *
     * @Rest\View(serializerGroups={"details"})
     * @Rest\Get("/category/{id}")
     */
    public function show(Category $category): View
    {
        return View::create($category, Response::HTTP_OK);
    }

    /**
     * Replaces Category resource.
     *
     * @Rest\Put("/category/{id}")
     */
    public function editCategory(Request $request, Category $category): View
    {
        if (null !== $category) {
            $category->setName($request->get('name'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        return View::create($category, Response::HTTP_OK);
    }
}
