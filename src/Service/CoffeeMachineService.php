<?php

namespace App\Service;

use App\Entity\Coffee;
use App\Entity\Ingredient;
use App\Entity\Transaction;
use App\Exception\MissingIngredientsException;
use App\Exception\MissingProductException;
use App\Exception\NotEnoughIngredientsException;
use App\Exception\ProductNotAvailableException;
use App\Exception\ProductNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Config\Ingredient as IngredientConfig;

readonly class CoffeeMachineService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getAvailableProducts(): array
    {
        return $this->entityManager->getRepository(Coffee::class)->getAvailableProducts();
    }

    /**
     * @throws ProductNotFoundException
     * @throws MissingIngredientsException
     * @throws MissingProductException
     * @throws NotEnoughIngredientsException
     * @throws ProductNotAvailableException
     */
    public function massBrew(string $product, int $quantity): void
    {
        $countPrepared = 1;

        while (true) {
            $this->brew($product);

            if ($countPrepared === $quantity) {
                break;
            }

            $countPrepared++;
        }
    }

    /**
     * @throws ProductNotFoundException
     * @throws MissingProductException
     * @throws ProductNotAvailableException
     * @throws NotEnoughIngredientsException
     * @throws MissingIngredientsException
     */
    public function brew(string $productName): void
    {
        if (empty($productName)) {
            throw new MissingProductException("No product specified");
        }

        $product = $this->entityManager->getRepository(Coffee::class)->findOneBy(["name" => $productName]);

        if (!$product instanceof Coffee) {
            throw new ProductNotFoundException(sprintf("Product %s not found", $productName));
        }

        if (empty($product->getAvailability())) {
            throw new ProductNotAvailableException(sprintf("Product %s is not available anymore", $productName));
        }

        $this->prepare($product);
        $this->save($product);
    }

    /**
     * @throws NotEnoughIngredientsException
     */
    private function checkIngredientsStock(Ingredient $ingredient, Coffee $product, string $ingredientName): bool
    {
        if ($ingredient->getQuantity() <= (int)$product->getContent()[$ingredientName]) {
            throw new NotEnoughIngredientsException(
                sprintf("There is not enough %s to brew %s", $ingredientName, $product->getName())
            );
        }

        return true;
    }

    /**
     * @throws NotEnoughIngredientsException
     * @throws MissingIngredientsException
     */
    private function prepare(Coffee $product): void
    {
        $ingredients = $this->entityManager->getRepository(Ingredient::class)->findAll();

        if (empty($ingredients)) {
            throw new MissingIngredientsException("There are no remaining ingredients at all");
        }

        /** @var Ingredient $ingredient */
        foreach ($ingredients as $ingredient) {
            $productContent = $product->getContent();

            switch ($ingredient->getName()) {
                case IngredientConfig::WATER->value:
                    if ($this->checkIngredientsStock($ingredient, $product, IngredientConfig::WATER->value)) {
                        $this->calculateQuantity($ingredient, (int)$productContent[IngredientConfig::WATER->value]);
                    }
                    break;
                case IngredientConfig::SUGAR->value:
                    if ($this->checkIngredientsStock($ingredient, $product, IngredientConfig::SUGAR->value)) {
                        $this->calculateQuantity($ingredient, (int)$productContent[IngredientConfig::SUGAR->value]);
                    }
                    break;
                case IngredientConfig::MILK->value:
                    if ($this->checkIngredientsStock($ingredient, $product, IngredientConfig::MILK->value)) {
                        $this->calculateQuantity($ingredient, (int)$productContent[IngredientConfig::MILK->value]);
                    }
                    break;
                case IngredientConfig::COFFEE->value:
                    if ($this->checkIngredientsStock($ingredient, $product, IngredientConfig::COFFEE->value)) {
                        $this->calculateQuantity($ingredient, (int)$productContent[IngredientConfig::COFFEE->value]);
                    }
                    break;
                default:
                    break;
            }
        }

        $product->setAvailability($product->getAvailability() - 1);
    }

    private function calculateQuantity(Ingredient $ingredient, int $quantity): void
    {
        $ingredient->setQuantity($ingredient->getQuantity() - $quantity);
    }

    private function save(Coffee $product): void
    {
        $transaction = (new Transaction())
            ->setPrice($product->getPrice())
            ->setProduct($product)
            ->setCreated();

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}