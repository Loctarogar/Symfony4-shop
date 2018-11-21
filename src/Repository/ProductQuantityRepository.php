<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Entity\ProductQuantity;
use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ProductQuantity|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductQuantity|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductQuantity[]    findAll()
 * @method ProductQuantity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductQuantityRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ProductQuantity::class);
    }

    /**
     * @param Cart $cart
     *
     * @return mixed
     */
    public function getAllForCart(Cart $cart)
    {
        $qb = $this->createQueryBuilder('pq')
            ->select('pq')
            ->andWhere('pq.cart = :cart')
            ->setParameter('cart', $cart)
            ->orderBy('pq.id', 'ASC');
        $qb = $qb->getQuery();

        return $qb->execute();
    }

    /**
     * @param Cart    $cart
     * @param Product $product
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getProductFromProductQuantity(Cart $cart, Product $product)
    {
        $qb = $this->createQueryBuilder('pq')
            ->select('pq')
            ->where('pq.cart = :cart')
            ->andWhere('pq.product = :product')
            ->setParameter('cart', $cart)
            ->setParameter('product', $product);
        $qb = $qb->getQuery();

        return $qb->getOneOrNullResult();
    }

//    /**
//     * @return ProductQuantity[] Returns an array of ProductQuantity objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductQuantity
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
