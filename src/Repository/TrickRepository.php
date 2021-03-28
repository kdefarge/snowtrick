<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function findAllJoinedToUserAndMedia() : array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t, u, m
            FROM App\Entity\Trick t
            INNER JOIN t.user u
            LEFT JOIN t.featured_media m
            ORDER BY t.id DESC'
        );

        return $query->getArrayResult();
    }

    public function findOneJoinedToUserAndCategory(string $slug) : ?Trick
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t, u, c, m
            FROM App\Entity\Trick t
            INNER JOIN t.user u
            INNER JOIN t.category c
            left JOIN t.featured_media m
            WHERE t.name = :trick_name
            ORDER BY t.id DESC'
        )->setParameter('trick_name', $slug);

        $result = $query->getResult();
        if(!$result)
            return null;
        
        $trick = $result[0];
        if ($trick instanceof Trick)
            return $trick;
        
        return false;
    }

    // /**
    //  * @return Trick[] Returns an array of Trick objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trick
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
