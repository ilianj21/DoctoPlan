<?php

// src/Command/GenerateTimeSlotsCommand.php

namespace App\Command;

use App\Entity\TimeSlot;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:generate:timeslots',
    description: 'Génère automatiquement les créneaux pour tous les médecins sur une plage de jours et d’horaires donnée.'
)]
class GenerateTimeSlotsCommand extends Command
{
    private EntityManagerInterface $em;
    private UserRepository          $userRepo;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepo)
    {
        parent::__construct();
        $this->em       = $em;
        $this->userRepo = $userRepo;
    }

    protected function configure(): void
    {
        $this
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Nombre de jours à générer depuis aujourd’hui', 30)
            ->addOption('interval', null, InputOption::VALUE_REQUIRED, 'Durée (en minutes) de chaque créneau', 30)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $days     = (int) $input->getOption('days');
        $interval = (int) $input->getOption('interval');

        // Récupérer tous les utilisateurs ayant ROLE_DOCTOR
        $doctors = $this->userRepo->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%ROLE_DOCTOR%')
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();

        $today = new \DateTimeImmutable('today');

        foreach ($doctors as $doctor) {
            for ($i = 0; $i < $days; $i++) {
                $date = $today->modify("+{$i} days");

                // Sauter samedis (6) et dimanches (7)
                if (in_array((int)$date->format('N'), [6, 7], true)) {
                    continue;
                }

                // Matin et après-midi
                foreach ([
                    ['start' => '08:00', 'end' => '12:00'],
                    ['start' => '13:00', 'end' => '18:00'],
                ] as $block) {
                    $blockStart = new \DateTimeImmutable("{$date->format('Y-m-d')} {$block['start']}");
                    $blockEnd   = new \DateTimeImmutable("{$date->format('Y-m-d')} {$block['end']}");

                    $cursor = $blockStart;
                    while ($cursor < $blockEnd) {
                        $slotEnd = $cursor->modify("+{$interval} minutes");

                        // Ne pas dupliquer un créneau existant
                        $exists = $this->em
                            ->getRepository(TimeSlot::class)
                            ->findOneBy([
                                'doctor'  => $doctor,
                                'startAt' => $cursor,
                            ])
                        ;

                        if (!$exists) {
                            $ts = new TimeSlot();
                            $ts->setDoctor($doctor);
                            $ts->setStartAt($cursor);
                            $ts->setEndAt($slotEnd);

                            $this->em->persist($ts);
                        }

                        $cursor = $slotEnd;
                    }
                }
            }
        }

        $this->em->flush();
        $output->writeln('<info>Les créneaux ont été générés avec succès.</info>');

        return Command::SUCCESS;
    }
}
