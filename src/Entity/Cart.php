<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CartRepository")
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="carts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="carts")
     */
    private $products;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isClosed = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductQuantity", mappedBy="cart", cascade={"persist"})
     * @Groups({"details"})
     */
    private $productQuantities;

    /**
     * @ORM\Column(type="float")
     */
    private $total_sum = 0; // я не можу це видалити, бо не знаю що потім робити з міграціями

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productQuantities = new ArrayCollection();
    }

    public function __toString()
    {
        return strval($this->getId());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }

    public function getIsClosed(): ?bool
    {
        return $this->isClosed;
    }

    public function setIsClosed(bool $isClosed): self
    {
        $this->isClosed = $isClosed;

        return $this;
    }

    /**
     * @return Collection|ProductQuantity[]
     */
    public function getProductQuantities(): Collection
    {
        return $this->productQuantities;
    }

    public function addProductQuantity(ProductQuantity $productQuantity): self
    {
        if (!$this->productQuantities->contains($productQuantity)) {
            $this->productQuantities[] = $productQuantity;
            $productQuantity->setCart($this);
        }

        return $this;
    }

    public function removeProductQuantity(ProductQuantity $productQuantity): self
    {
        if ($this->productQuantities->contains($productQuantity)) {
            $this->productQuantities->removeElement($productQuantity);
            // set the owning side to null (unless already changed)
            if ($productQuantity->getCart() === $this) {
                $productQuantity->setCart(null);
            }
        }

        return $this;
    }
}
