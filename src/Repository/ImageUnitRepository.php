<?php

namespace App\Repository;

use App\Entity\ImageUnit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageUnit|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageUnit|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageUnit[]    findAll()
 * @method ImageUnit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageUnitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageUnit::class);
    }

    // /**
    //  * @return ImageUnit[] Returns an array of ImageUnit objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImageUnit
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
