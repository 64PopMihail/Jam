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
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $datetime;

    #[ORM\Column(type: 'float')]
    private $total;

    #[ORM\OneToMany(mappedBy: 'order_associated', targetEntity: LineOrder::class, cascade: ["persist"])]
    private $lineOrders;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    public function __construct()
    {
        $this->lineOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

  
    /**
     * @return Collection|LineOrder[]
     */
    public function getLineOrders(): Collection
    {
        return $this->lineOrders;
    }

    public function addLineOrder(LineOrder $lineOrder): self
    {
        if (!$this->lineOrders->contains($lineOrder)) {
            $this->lineOrders[] = $lineOrder;
            $lineOrder->setOrderAssociated($this);
        }

        return $this;
    }

    public function removeLineOrder(LineOrder $lineOrder): self
    {
        if ($this->lineOrders->removeElement($lineOrder)) {
            // set the owning side to null (unless already changed)
            if ($lineOrder->getOrderAssociated() === $this) {
                $lineOrder->setOrderAssociated(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function __toString()
    {
        return $this->id;
    }
}
