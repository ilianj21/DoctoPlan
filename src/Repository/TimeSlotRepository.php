<?php


namespace App\Repository;

use App\Entity\TimeSlot;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Appointment;

class TimeSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeSlot::class);
    }

    /**
     * Renvoie les créneaux disponibles pour un médecin (non réservés)
     * @return TimeSlot[]
     */
    public function findAvailableSlotsForDoctor(User $doctor): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin(Appointment::class, 'a', 'WITH', 'a.timeSlot = t')
            ->andWhere('t.doctor = :doctor')
            ->andWhere('a.id IS NULL')
            ->setParameter('doctor', $doctor)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
