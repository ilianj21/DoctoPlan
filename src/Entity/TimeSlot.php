<?php

namespace App\Entity;

use App\Repository\TimeSlotRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: TimeSlotRepository::class)]
class TimeSlot
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotNull]
    #[Assert\GreaterThan('now', message: 'Le début du créneau doit être dans le futur.')]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $doctor = null;

    // … vos autres propriétés / collections …

    public function getId(): ?int         { return $this->id; }
    public function getStartAt(): ?\DateTimeImmutable  { return $this->startAt; }
    public function setStartAt(\DateTimeImmutable $dt): self { $this->startAt = $dt; return $this; }
    public function getEndAt(): ?\DateTimeInterface   { return $this->endAt; }
    public function setEndAt(\DateTimeInterface $dt): self   { $this->endAt   = $dt; return $this; }
    public function getDoctor(): ?User    { return $this->doctor; }
    public function setDoctor(User $d): self { $this->doctor = $d; return $this; }

    /**
     * Vérifie heure de début/fin et plage autorisée.
     */
    #[Assert\Callback]
    public function validateHours(ExecutionContextInterface $ctx)
    {
        if (!$this->startAt || !$this->endAt) {
            return;
        }

        // fin doit être après début
        if ($this->endAt <= $this->startAt) {
            $ctx->buildViolation('La fin doit être après le début.')
                ->atPath('endAt')
                ->addViolation();
        }

        // récupère l’heure (int) de début et fin
        $h1 = (int)$this->startAt->format('H');
        $h2 = (int)$this->endAt->format('H');

        $inMorning = $h1 >= 8  && $h2 <= 13;
        $inAfternoon = $h1 >= 14 && $h2 <= 18;

        if (!($inMorning || $inAfternoon)) {
            $ctx->buildViolation('Heures autorisées : 08:00–13:00 ou 14:00–18:00.')
                ->atPath('startAt')
                ->addViolation();
        }
    }
}
