<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private int $id;

    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private string $name;

    #[ORM\OneToMany(mappedBy: "category", targetEntity: Product::class)]
    #[Groups(['category_all'])]
    private Collection $products;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private string $description;

    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private bool $active;

    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['category_all', 'product_all'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDescription(): string
    {
        return $this->description;
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

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
