<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass="App\Repository\DiscountRepository")
 */
class Discount
{
    public const FIX_PRICE = 'fix_price';
    public const PERCENT = 'percent';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_time;

    /**
     * @ORM\Column(type="datetime")
     */
    private $stop_time;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @Groups({"details"})
     */
    private $discount_type;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"details"})
     */
    private $value;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", inversedBy="discounts")
     */
    private $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProductQuantity", mappedBy="discount")
     */
    private $productQuantities;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->productQuantities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): self
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getStopTime(): ?\DateTimeInterface
    {
        return $this->stop_time;
    }

    public function setStopTime(\DateTimeInterface $stop_time): self
    {
        $this->stop_time = $stop_time;

        return $this;
    }

    public function getDiscountType(): ?string
    {
        return $this->discount_type;
    }

    public function setDiscountType(string $discount_type): self
    {
        $this->discount_type = $discount_type;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

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
            $productQuantity->setDiscount($this);
        }

        return $this;
    }

    public function removeProductQuantity(ProductQuantity $productQuantity): self
    {
        if ($this->productQuantities->contains($productQuantity)) {
            $this->productQuantities->removeElement($productQuantity);
            // set the owning side to null (unless already changed)
            if ($productQuantity->getDiscount() === $this) {
                $productQuantity->setDiscount(null);
            }
        }

        return $this;
    }
}
