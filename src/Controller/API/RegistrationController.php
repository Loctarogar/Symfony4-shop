<?php declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

class RegistrationController extends FOSRestController
{
    /**
     * @Rest\Post("/register", name="user_registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $user->setUsername($request->get('username'));
        $password = $passwordEncoder->encodePassword($user, $request->get('plainPassword')); // todo: винести в listener. це на потім
        $user->setPassword($password);
        $user->setEmail($request->get('email'));
        $user->setRoles(['ROLE_ADMIN']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('product_controller');
    }
}
