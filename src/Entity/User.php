<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, TimeSlot>
     */
    #[ORM\OneToMany(targetEntity: TimeSlot::class, mappedBy: 'doctor')]
    private Collection $timeSlots;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'patient')]
    private Collection $appointements;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'doctor')]
    private Collection $doctorAppointments;

    /**
     * @var Collection<int, Notification>
     */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    public function __construct()
    {
        $this->timeSlots = new ArrayCollection();
        $this->appointements = new ArrayCollection();
        $this->doctorAppointments = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Collection<int, TimeSlot>
     */
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }

    public function addTimeSlot(TimeSlot $timeSlot): static
    {
        if (!$this->timeSlots->contains($timeSlot)) {
            $this->timeSlots->add($timeSlot);
            $timeSlot->setDoctor($this);
        }

        return $this;
    }

    public function removeTimeSlot(TimeSlot $timeSlot): static
    {
        if ($this->timeSlots->removeElement($timeSlot)) {
            // set the owning side to null (unless already changed)
            if ($timeSlot->getDoctor() === $this) {
                $timeSlot->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getAppointements(): Collection
    {
        return $this->appointements;
    }

    public function addAppointement(Appointment $appointement): static
    {
        if (!$this->appointements->contains($appointement)) {
            $this->appointements->add($appointement);
            $appointement->setPatient($this);
        }

        return $this;
    }

    public function removeAppointement(Appointment $appointement): static
    {
        if ($this->appointements->removeElement($appointement)) {
            // set the owning side to null (unless already changed)
            if ($appointement->getPatient() === $this) {
                $appointement->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointment>
     */
    public function getDoctorAppointments(): Collection
    {
        return $this->doctorAppointments;
    }

    public function addDoctorAppointment(Appointment $doctorAppointment): static
    {
        if (!$this->doctorAppointments->contains($doctorAppointment)) {
            $this->doctorAppointments->add($doctorAppointment);
            $doctorAppointment->setDoctor($this);
        }

        return $this;
    }

    public function removeDoctorAppointment(Appointment $doctorAppointment): static
    {
        if ($this->doctorAppointments->removeElement($doctorAppointment)) {
            // set the owning side to null (unless already changed)
            if ($doctorAppointment->getDoctor() === $this) {
                $doctorAppointment->setDoctor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
