<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Date;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    #[Groups(['user_all'])]
    private int $id;

    #[ORM\Column(type: "string", unique: true)]
    #[Groups(['user_all'])]
    private string $email;
    
    #[ORM\Column(type: 'json')]
    #[Groups(['user_all'])]
    private array $roles = [];

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: "users")]
    #[Groups(['user_all'])]
    private Company $company;

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private string $firstName;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private string $lastName;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private ?string $cpf;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private ?string $phoneNumber;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private ?string $state;

    #[ORM\Column]
    #[Groups(['user_all'])]
    private ?string $city;

    #[ORM\Column(type: 'date')]
    #[Groups(['user_all'])]
    private ?DateTimeInterface $dob;

    public function getId(): int
    {
        return $this->id;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setDob(?DateTimeInterface $dob): void
    {
        $this->dob = $dob;
    }

    public function getDob(): ?DateTimeInterface
    {
        return $this->dob;
    }

    public function getUserIdentifier(): string { return $this->email; }
    public function getRoles(): array { return array_unique(array_merge($this->roles, ['ROLE_USER'])); }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }
    public function eraseCredentials(): void {}
}
