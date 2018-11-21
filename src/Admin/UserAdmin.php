<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\UserGroupes;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Sonata\AdminBundle\Form\Type\ModelType;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('isActive', BooleanType::class)
            ->add('plainPassword', PasswordType::class)
            ->add('roles', ChoiceType::class, [
                'multiple' => true,
                'choices' => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                    'Seller' => 'ROLE_SELLER',
                    'SuperAdmin' => 'ROLE_SUPER_ADMIN',
                ], ])
            ->add('userGroupes', ModelType::class, [
                'class' => UserGroupes::class,
                'property' => 'name',
                'multiple' => true,
                'required' => false,
            ])
            ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('isActive')
            ->add('roles')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('isActive')
            ->add('roles')
        ;
    }
}
