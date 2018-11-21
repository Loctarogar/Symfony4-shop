<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $entityManager;
    private $encoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
    }

    public function createUser($name, $password, $email)
    {
        $user = new User();
        $user->setUsername($name);
        $hashedPassword = $this->encoder->encodePassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setRoles([User::ROLE_ADMIN]);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}