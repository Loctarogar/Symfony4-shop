<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    const DELETE = 'delete';
    const EDIT = 'edit';
    const CREATE = 'create';

    protected function supports($attribute, $subject)
    {
        // you only want to vote if the attribute and subject are what you expect
        return self::DELETE === $attribute && $subject instanceof Product;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /* @var $user User */
        $user = $token->getUser();

        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
