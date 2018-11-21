<?php declare(strict_types=1);

namespace App\Doctrine\Filter;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Configurator
{
    protected $em;
    protected $tokenStorage;
    protected $requestStack;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest()
    {
        $a = $this->requestStack->getCurrentRequest()->getPathInfo();
        if (preg_match('(^admin\/.*)', $a)) {
            $entity_filter = $this->em->getFilters()->disable('discount_filter');
        }
    }

    private function getUser()
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        if (!($user instanceof UserInterface)) {
            return null;
        }

        return $user;
    }
}
