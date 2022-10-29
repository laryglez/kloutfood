<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'recipes')]
    private Collection $orders;

    #[ORM\OneToMany(mappedBy: 'recipe', targetEntity: RecipeProduct::class, orphanRemoval: true)]
    private Collection $recipeProducts;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->recipeProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->addRecipe($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeRecipe($this);
        }

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
            $recipeProduct->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeProduct(RecipeProduct $recipeProduct): self
    {
        if ($this->recipeProducts->removeElement($recipeProduct)) {
            // set the owning side to null (unless already changed)
            if ($recipeProduct->getRecipe() === $this) {
                $recipeProduct->setRecipe(null);
            }
        }

        return $this;
    }
}
