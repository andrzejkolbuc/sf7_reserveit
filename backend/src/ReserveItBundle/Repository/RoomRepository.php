<?php

namespace App\ReserveItBundle\Repository;

use App\ReserveItBundle\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findAvailable(\DateTime $startTime, \DateTime $endTime): array
    {
        return $this->createQueryBuilder('r')
            ->where('NOT EXISTS (
                SELECT 1 FROM App\ReserveItBundle\Entity\Reservation res
                WHERE res.room = r
                AND res.startTime < :endTime
                AND res.endTime > :startTime
            )')
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime)
            ->getQuery()
            ->getResult();
    }
}
