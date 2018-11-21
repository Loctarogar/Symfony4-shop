<?php declare(strict_types=1);

namespace App\Controller\API;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\Tag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\TagRepository;

// Add /api for Postman
// Запитати за Redmine

class TagController extends FOSRestController
{
    //An Error:
    //An instance of Symfony\Bundle\FrameworkBundle\Templating\EngineInterface must be injected in FOS\RestBundle\View\ViewHandler to render templates.
    // To get rid of an error change:
    //- { path: ^/api, prefer_extension: true, fallback_format: json, priorities: [ json, html ] }
    //  to
    //- { path: ^/api, prefer_extension: true, fallback_format: json, priorities: [ json ] }

    /**
     * Retrieves a collection of Tag resource.
     *
     * @Rest\View(serializerGroups={"details"})
     * @Rest\Get("/tag")
     */
    public function index(TagRepository $tagRepository): View
    {
        $data = $tagRepository->findAll();

        return View::create($data, Response::HTTP_OK);
    }

    /**
     * Creates a Tag.
     *
     * @Rest\Post("/tag/add")
     *
     * @param Request $request
     *
     * @return View
     */
    public function addTag(Request $request): View
    {
        $tag = new Tag();
        $tag->setTitle($request->get('title'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($tag);
        $em->flush();

        return View::create($tag, Response::HTTP_CREATED);
    }

    /**
     * Retrieves single Tag.
     *
     * @Rest\View(serializerGroups={"details"})
     * @Rest\Get("/tag/{id}")
     */
    public function show(Tag $tag): View
    {
        return View::create($tag, Response::HTTP_OK);
    }

    /**
     * Replaces Tag resource.
     *
     * @Rest\Put("/tag/{id}")
     */
    public function editTag(Request $request, Tag $tag): View
    {
        if (null !== $tag) {
            $tag->setTitle($request->get('title'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($tag);
            $em->flush();
        }

        return View::create($tag, Response::HTTP_OK);
    }
}
