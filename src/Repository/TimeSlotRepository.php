<?php
// src/Repository/TimeSlotRepository.php

namespace App\Repository;

use App\Entity\TimeSlot;
use App\Entity\User;
use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TimeSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeSlot::class);
    }

    /**
     * Retourne les créneaux du médecin qui se chevauchent avec l’intervalle donné.
     *
     * @return TimeSlot[]
     */
    public function findOverlappingSlots(User $doctor, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.doctor = :doctor')
            ->andWhere('t.startAt < :end')
            ->andWhere('t.endAt   > :start')
            ->setParameter('doctor', $doctor)
            ->setParameter('start',  $start)
            ->setParameter('end',    $end)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les créneaux disponibles (non réservés) pour un médecin donné.
     *
     * @return TimeSlot[]
     */
    public function findAvailableSlotsForDoctor(User $doctor): array
    {
        return $this->createQueryBuilder('t')
            // on joint l'entité Appointment, sans passer par une relation inverse
            ->leftJoin(Appointment::class, 'a', 'WITH', 'a.timeSlot = t')
            ->andWhere('t.doctor = :doctor')
            ->andWhere('a.id IS NULL') // seuls les slots sans rendez-vous associé
            ->setParameter('doctor', $doctor)
            ->orderBy('t.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
