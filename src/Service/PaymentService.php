<?php

namespace App\Service;

use App\Config\PaymentRange;
use App\Entity\Coffee;
use App\Exception\InvalidAmountException;
use App\Exception\MissingProductException;
use App\Exception\NotEnoughAmountException;
use App\Exception\ProductNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class PaymentService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws MissingProductException
     * @throws ProductNotFoundException
     * @throws NotEnoughAmountException
     * @throws InvalidAmountException
     */
    public function calculatePrice(string $productName, string $amount, int $quantity): float
    {
        $min = PaymentRange::MIN->value;
        $max = PaymentRange::MAX->value;

        if (!is_numeric($amount) || (float)$amount <= $min || (float)$amount > $max) {
            throw new InvalidAmountException(
                sprintf("This is not a valid amount. Only numeric values between %d and %d are allowed", $min, $max)
            );
        }

        if (empty($productName)) {
            throw new MissingProductException("No product specified");
        }

        $product = $this->entityManager->getRepository(Coffee::class)->findOneBy(["name" => $productName]);

        if (!$product instanceof Coffee) {
            throw new ProductNotFoundException(sprintf("Product %s not found", $productName));
        }

        $priceToPay = $product->getPrice() * $quantity;

        return match ($priceToPay <=> $amount) {
            1 => throw new NotEnoughAmountException(
                sprintf("The amount you payed is not enough. You should pay %d euros", $priceToPay)
            ),
            -1 => abs($priceToPay - $amount),
            default => 0
        };
    }
}