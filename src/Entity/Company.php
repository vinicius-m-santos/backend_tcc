<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['company_all', 'company_only'])]
    private int $id;

    #[ORM\Column]
    #[Groups(['company_all', 'company_only'])]
    private string $name;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: "company")]
    #[Groups(['company_all'])]
    private Collection $users;

    #[ORM\Column]
    #[Groups(['company_all', 'company_only'])]
    private bool $active;

    #[ORM\Column]
    #[Groups(['company_all', 'company_only'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['company_all', 'company_only'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
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

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}
