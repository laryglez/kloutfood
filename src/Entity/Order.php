<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $owner = null;

    #[ORM\ManyToMany(targetEntity: recipe::class, inversedBy: 'orders')]
    private Collection $recipes;

    private float $subtotal;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
        $this->subtotal = 0.0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtotal()
    {
        return $this->subtotal;
    }

    public function getOwner(): ?user
    {
        return $this->owner;
    }

    public function setOwner(?user $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes->add($recipe);
        }

        return $this;
    }

    public function removeRecipe(recipe $recipe): self
    {
        $this->recipes->removeElement($recipe);

        return $this;
    }

    public function groupingProducts(): float 
    {
        $result = array();
        foreach($this->recipes as $recipe)
        {
            $recipe_products = $recipe->getRecipeProducts();
            foreach($recipe_products as $recipe_product)
            {
                if(isset($result[$recipe_product->getProduct()->getId()]))
                {
                    $result[$recipe_product->getProduct()->getId()]["sum"] += $recipe_product->getAmount();
                }
                else
                {
                    $result[$recipe_product->getProduct()->getId()] = array("sum"=> $recipe_product->getAmount(), "product"=> $recipe_product->getProduct());
                }
            }
        }
        $summation_products = $this->minProducts($result);

        return $result;
    }

    public function minProducts($summation_products): array
    {
        $result = array();
        foreach($summation_products as $product_id => $product)
        {
            if($product["product"]->getAmount() < $product["sum"] )
            {
                $units = ceil($product["sum"] / $product["product"]->getAmount());
                $result[$product_id] = $units;
                $this->subtotal += $units*$product["product"]->getPrice();
            }
            else
            {
                $result[$product_id] = 1;
                $this->subtotal += $product["product"]->getPrice();
            }
        }

        return $result;
    }
}
