<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?int $availability = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: RecipeProduct::class, orphanRemoval: true)]
    private Collection $recipeProducts;

    public function __construct()
    {
        $this->recipeProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAvailability(): ?int
    {
        return $this->availability;
    }

    public function setAvailability(int $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    /**
     * @return Collection<int, RecipeProduct>
     */
    public function getRecipeProducts(): Collection
    {
        return $this->recipeProducts;
    }

    public function addRecipeProduct(RecipeProduct $recipeProduct): self
    {
        if (!$this->recipeProducts->contains($recipeProduct)) {
            $this->recipeProducts->add($recipeProduct);
            $recipeProduct->setProduct($this);
        }

        return $this;
    }

    public function removeRecipeProduct(RecipeProduct $recipeProduct): self
    {
        if ($this->recipeProducts->removeElement($recipeProduct)) {
            // set the owning side to null (unless already changed)
            if ($recipeProduct->getProduct() === $this) {
                $recipeProduct->setProduct(null);
            }
        }

        return $this;
    }
}
