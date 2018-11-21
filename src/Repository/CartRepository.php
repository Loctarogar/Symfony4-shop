<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function findByOwner($user)
    {
        $user_cart = $this->findOneBy(['owner' => $user, 'isClosed' => false]);

        return $user_cart;
    }

    /*
     * Те ж що і findOwner але через QueryBuilder.
     *
     * @param $user
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    /*
    public function findOwnerQB($user)
    {
        return $this->createQueryBuilder('cart')
            ->where('cart.isClosed = false')
            ->andWhere('cart.owner = :owner')->setParameter('owner', $user)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
    */

    /*
    public function findOwner($owner)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery(
            'SELECT cart AS c
            FROM App\Entity\Cart cart
            WHERE cart.owner = :owner'
        )->setParameter('owner', $owner);
        return $query->execute();
    }
*/

//    /**
//     * @return Cart[] Returns an array of Cart objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cart
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
