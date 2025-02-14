<?php

namespace App\ReserveItBundle\Repository;

use App\ReserveItBundle\Entity\Reservation;
use App\ReserveItBundle\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findOverlapping(Room $room, \DateTime $startTime, \DateTime $endTime, ?int $excludeId = null): ?Reservation
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.room = :room')
            ->andWhere('r.startTime < :endTime')
            ->andWhere('r.endTime > :startTime')
            ->setParameter('room', $room)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime);

        if ($excludeId) {
            $qb->andWhere('r.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()
                 ->getOneOrNullResult();
    }
}
