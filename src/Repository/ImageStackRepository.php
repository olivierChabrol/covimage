<?php

namespace App\Repository;

use App\Entity\ImageStack;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImageStack|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImageStack|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImageStack[]    findAll()
 * @method ImageStack[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageStackRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImageStack::class);
    }

    // /**
    //  * @return ImageStack[] Returns an array of ImageStack objects
    //  */
    
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
    

    
    public function findOneBySomeField($value): ?ImageStack
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    
}
