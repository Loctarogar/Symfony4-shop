<?php

namespace App\Repository;

use App\Entity\UserGroupes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserGroupes|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroupes|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroupes[]    findAll()
 * @method UserGroupes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGroupesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserGroupes::class);
    }

//    /**
//     * @return UserGroupes[] Returns an array of UserGroupes objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserGroupes
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
