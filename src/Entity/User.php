<?php
// src/Entity/User.php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L’email est obligatoire.')]
    #[Assert\Email(message: 'L’email "{{ value }}" n’est pas valide.')]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    private ?string $name = null;

    
    /**
     * @var Collection<int, TimeSlot>
     */
    #[ORM\OneToMany(targetEntity: TimeSlot::class, mappedBy: 'doctor')]
    private Collection $timeSlots;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'patient')]
    private Collection $appointments;

    /**
     * @var Collection<int, Appointment>
     */
    #[ORM\OneToMany(targetEntity: Appointment::class, mappedBy: 'doctor')]
    private Collection $doctorAppointments;


    public function __construct()
    {
        $this->timeSlots          = new ArrayCollection();
        $this->appointments       = new ArrayCollection();
        $this->doctorAppointments = new ArrayCollection();
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


    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    
    public function getUsername(): string 
    {
        return $this->email;  
    }


     public function getRoles(): array 
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';         
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = array_unique($roles);
        return $this;
    }

     public function getSalt(): ?string 
    {
        return null;
    }

    public function getPassword(): string 
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

   public function eraseCredentials(): void
    {
        $this->plainPassword = null;
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
    
    public function getTimeSlots(): Collection
    {
        return $this->timeSlots;
    }
    public function addTimeSlot(TimeSlot $ts): static { /* … */ }
    public function removeTimeSlot(TimeSlot $ts): static { /* … */ }

    public function getAppointments(): Collection
    {
        return $this->appointments;
    }
    public function addAppointment(Appointment $a): static { /* … */ }
    public function removeAppointment(Appointment $a): static { /* … */ }

    public function getDoctorAppointments(): Collection
    {
        return $this->doctorAppointments;
    }
    public function addDoctorAppointment(Appointment $a): static { /* … */ }
    public function removeDoctorAppointment(Appointment $a): static { /* … */ }

    
}
