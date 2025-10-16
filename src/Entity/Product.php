<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['product_all', 'sale_all', 'top_five_most_sold'])]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    private Company $company;

    #[ORM\ManyToOne(inversedBy: "products")]
    #[Groups(['product_all'])]
    private Category $category;

    #[ORM\ManyToMany(mappedBy: 'products', targetEntity: Sale::class)]
    #[Groups(['product_sales'])]
    private Collection $sales;

    #[ORM\Column]
    #[Groups(['product_all', 'sale_all', 'top_five_most_sold'])]
    private string $name;

    #[ORM\Column]
    #[Groups(['product_all'])]
    private string $description;

    #[ORM\Column]
    #[Groups(['product_all', 'sale_all'])]
    private float $price;

    #[ORM\Column]
    #[Groups(['product_all', 'top_five_most_sold'])]
    private int $quantityInStock;

    #[ORM\Column]
    #[Groups(['product_all'])]
    private bool $active;

    #[ORM\Column]
    #[Groups(['product_all'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Groups(['product_all'])]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->sales = new ArrayCollection();
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

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getQuantityInStock(): int
    {
        return $this->quantityInStock;
    }

    public function setQuantityInStock(int $quantityInStock): void
    {
        $this->quantityInStock = $quantityInStock;
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

    public function addSale(Sale $sale): static
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
        }

        return $this;
    }

    public function removeSale(Sale $sale): static
    {
        $this->sales->removeElement($sale);

        return $this;
    }

    public function setCompany(Company $company): void
    {
        $this->company = $company;
    }
}
