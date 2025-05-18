<?php

namespace App\Repository;

use App\Entity\TimeSlot;
use App\Entity\User;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeSlot>
 */
class TimeSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeSlot::class);
    }

    /**
     * Renvoie les créneaux du même médecin qui se chevauchent
     *
     * @param User              $doctor
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @return TimeSlot[]
     */
    public function findOverlappingSlots(User $doctor, DateTimeInterface $start, DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.doctor = :doctor')
            ->andWhere('t.startAt < :end')
            ->andWhere('t.endAt   > :start')
            ->setParameter('doctor', $doctor)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Renvoie les créneaux libres (non réservés) pour un médecin
     *
     * @param User $doctor
     * @return TimeSlot[]
     */
    public function findAvailableSlotsForDoctor(User $doctor): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.appointments', 'a')   // ou bien leftJoin(Appointment::class,'a','WITH','a.timeSlot = t')
            ->andWhere('t.doctor = :doctor')
            ->andWhere('a.id IS NULL')
            ->setParameter('doctor', $doctor)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
