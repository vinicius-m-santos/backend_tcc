<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['client_all'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private string $firstName;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private string $lastName;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private string $cellphoneNumber;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private string $email;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private int $age;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private bool $active;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['client_all'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getCellphoneNumber(): string
    {
        return $this->cellphoneNumber;
    }

    public function setCellphoneNumber(string $cellphoneNumber): void
    {
        $this->cellphoneNumber = $cellphoneNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
