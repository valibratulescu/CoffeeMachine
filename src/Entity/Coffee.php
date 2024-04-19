<?php

namespace App\Entity;

use App\Repository\CoffeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CoffeeRepository::class)]
#[ORM\Table(name: "products")]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "type", type: Types::STRING)]
#[ORM\DiscriminatorMap(
    [
        Coffee::TYPE         => Coffee::class,
        Espresso::TYPE       => Espresso::class,
        LatteMacchiato::TYPE => LatteMacchiato::class,
        Cappuccino::TYPE     => Cappuccino::class,
    ]
)]
class Coffee
{
    public const string TYPE = "coffee";

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int|null $id;

    #[ORM\Column(name: "name", type: Types::STRING, length: 255, unique: true, nullable: false)]
    private string $name;

    #[ORM\Column(name: "price", type: Types::FLOAT, nullable: false)]
    private float $price;

    #[ORM\Column(name: "availability", type: Types::INTEGER, nullable: false)]
    private int $availability;

    #[ORM\Column(name: "content", type: Types::JSON, nullable: false)]
    private array $content;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: "product", cascade: ["persist", "remove"])]
    private Collection $transactions;

    private string $type = self::TYPE;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
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

    public function setAvailability(int $availability): self
    {
        $this->availability = $availability;

        return $this;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    public function setContent(array $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setProduct($this);
        }

        return $this;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function toArray(): array
    {
        return [
            "name"         => $this->name,
            "price"        => $this->price,
            "availability" => $this->availability,
            "content"      => str_replace(["{", "}", "\""], ["", "", ""], json_encode($this->content))
        ];
    }
}