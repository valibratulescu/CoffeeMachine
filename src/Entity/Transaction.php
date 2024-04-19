<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
#[ORM\Table(name: "transactions")]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id;

    #[ORM\Column(name: "price", type: Types::FLOAT, nullable: false)]
    private float $price;

    #[ORM\ManyToOne(targetEntity: Coffee::class, inversedBy: "transactions")]
    #[ORM\JoinColumn(nullable: false)]
    private Coffee $product;

    #[Gedmo\Timestampable(on: "create")]
    #[ORM\Column(name: "created", type: Types::DATETIME_MUTABLE, nullable: false)]
    private DateTime $created;

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setProduct(Coffee $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): Coffee
    {
        return $this->product;
    }

    public function setCreated(): self
    {
        $this->created = new DateTime();

        return $this;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }
}