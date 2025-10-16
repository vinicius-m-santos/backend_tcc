<?php

namespace App\Entity;

use App\Repository\ExpenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseRepository::class)]
class Expense
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense_all', 'expense_category_all'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    #[Groups(['expense_all', 'expense_category_all'])]
    private string $name;

    #[ORM\Column]
    #[Groups(['expense_all'])]
    private string $description;

    #[ORM\Column]
    #[Groups(['expense_all', 'expense_list'])]
    private float $total;

    #[ORM\ManyToOne(targetEntity: ExpenseCategory::class, inversedBy: 'expenses')]
    #[Groups(['expense_all'])]
    private ?ExpenseCategory $expenseCategory;

    #[ORM\Column]
    #[Groups(['expense_all', 'expense_category_all'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['expense_all', 'expense_category_all'])]
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function setExpenseCategory(ExpenseCategory $expenseCategory): static
    {
        $this->expenseCategory = $expenseCategory;

        return $this;
    }

    public function removeExpenseCategory(): static
    {
        $this->expenseCategory = null;

        return $this;
    }

    public function getExpenseCategory(): ?ExpenseCategory
    {
        return $this->expenseCategory;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
