<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Payment as BasePayment;

/**
 * @ORM\Table
 * @ORM\Entity
 */
class Payment extends BasePayment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order", inversedBy="payment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userOrder;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserOrder(): ?Order
    {
        return $this->userOrder;
    }

    public function setUserOrder(Order $userOrder): self
    {
        $this->userOrder = $userOrder;

        return $this;
    }
}
