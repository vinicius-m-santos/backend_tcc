<?php

namespace App\Entity;

use App\Repository\ExpenseCategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseCategoryRepository::class)]
class ExpenseCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['expense_category_all', 'expense_all'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    #[Groups(['expense_category_all', 'expense_all'])]
    private string $name;

    #[ORM\OneToMany(targetEntity: Expense::class, mappedBy: 'expenseCategory', cascade: ['persist', 'remove'])]
    #[Groups(['expense_category_all'])]
    private Collection $expenses;

    #[ORM\Column]
    #[Groups(['expense_category_all', 'expense_all'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['expense_category_all', 'expense_all'])]
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

    public function addExpense(Expense $expense): static
    {
        if (!$this->expenses->contains($expense)) {
            $this->expenses->add($expense);
            $expense->setExpenseCategory($this);
        }

        return $this;
    }

    public function removeExpense(Expense $expense): static
    {
        if ($this->expenses->removeElement($expense)) {
            $expense->removeExpenseCategory();
        }

        return $this;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
