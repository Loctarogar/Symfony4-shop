<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @param array $tags
     * @param null  $category
     * @param null  $minPrice
     * @param null  $maxPrice
     * @param null  $order
     *
     * @return mixed
     */
    public function findByFilters($category = null, $minPrice = null, $maxPrice = null, $order = null, array $tags = [])
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p');
        if (null !== $maxPrice) {
            $qb->andWhere('p.price <= :maxPrice')->setParameter('maxPrice', $maxPrice);
        }
        if (null !== $minPrice) {
            $qb->andWhere('p.price >= :minPrice')->setParameter('minPrice', $minPrice);
        }
        if (null !== $category) {
            $qb->andWhere('p.category = :category')->setParameter('category', $category);
        }
        if (!empty($tags)) {
            $qb->innerJoin('p.tag', 'tags');
            $qb->andWhere('tags IN(:tags)')->setParameter('tags', $tags);
        }
        if (null !== $order) {
            $qb->orderBy('p.price', $order);  // працює
        }
        $qb = $qb->getQuery();

        return $qb->execute();
    }

//    /**
//     * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
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
