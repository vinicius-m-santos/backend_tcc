<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list'])]
    private string $name;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list'])]
    private string $description;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list', 'top_five_most_sold'])]
    private int $quantity;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'sales')]
    #[Groups(['sale_all', 'top_five_most_sold'])]
    private Collection $products;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list', 'top_five_most_sold'])]
    private float $total = 0;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['sale_all', 'sale_list'])]
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

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->addSale($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            $product->removeSale($this);
        }

        return $this;
    }

    public function clearProducts(): static
    {
        $this->products = new ArrayCollection();

        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
