<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Table(name="user_order")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    public const STATUS_CANCELLED = 'status_cancelled';
    public const STATUS_ACCEPTED = 'status_accepted';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="users_order")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"details"})
     */
    private $user;

    /**
     * @ORM\Column(name="deletedAt", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cart")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"details"})
     */
    private $cart;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"details"})
     */
    private $isPaid = false;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", mappedBy="userOrder", cascade={"persist", "remove"})
     */
    private $payment;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $payuorderid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): self
    {
        $this->payment = $payment;

        // set the owning side of the relation if necessary
        if ($this !== $payment->getUserOrder()) {
            $payment->setUserOrder($this);
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPayuorderid(): ?string
    {
        return $this->payuorderid;
    }

    public function setPayuorderid(?string $payuorderid): self
    {
        $this->payuorderid = $payuorderid;

        return $this;
    }
}
