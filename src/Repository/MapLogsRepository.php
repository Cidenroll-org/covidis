<?php

namespace App\Repository;

use App\Entity\MapLogs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MapLogs|null find($id, $lockMode = null, $lockVersion = null)
 * @method MapLogs|null findOneBy(array $criteria, array $orderBy = null)
 * @method MapLogs[]    findAll()
 * @method MapLogs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapLogsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MapLogs::class);
    }

    public function findByRedisKey($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.redisKey = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?MapLogs
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
