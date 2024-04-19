<?php

namespace App\Command;

use App\Config\PaymentRange;
use App\Config\QuantityRange;
use App\Entity\Coffee;
use App\Exception\InvalidAmountException;
use App\Exception\InvalidQuantityException;
use App\Exception\MissingProductsException;
use App\Service\CoffeeMachineService;
use App\Service\PaymentService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Lock\LockFactory;
use Throwable;

#[AsCommand(
    name: "app:coffee-machine",
    description: "Prepare the desired coffee",
    aliases: [],
    hidden: false
)]
class CoffeeMachineCommand extends Command
{
    private const string LOCK_KEY = "coffee-machine-command";
    private const array PRODUCTS_TABLE_HEADER = ["Name", "Price", "Availability", "Content"];

    public function __construct(
        private readonly CoffeeMachineService $coffeeMachineService,
        private readonly PaymentService $paymentService,
        private readonly LockFactory $lockFactory,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $lock = $this->lockFactory->createLock(static::LOCK_KEY);

        if (!$lock->acquire()) {
            $io->warning("The coffee machine is already preparing some coffee right now");
            return Command::INVALID;
        }

        try {
            $products = $this->getProducts();
            $this->listAvailableProducts($output, $products);
            $product = $this->selectProduct($input, $output, $products);
            $quantity = $this->selectQuantity($input, $output, $products, $product);
            $this->pay($input, $output, $product, $quantity);
            $this->coffeeMachineService->massBrew($product, $quantity);

            if ($quantity === 0) {
                $io->success("Your {$product} is ready. Enjoy!");
            }

            if ($quantity > 0) {
                $io->success("Your {$product}s are ready. Enjoy!");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        } finally {
            $lock->release();
        }
    }

    /**
     * @throws MissingProductsException
     */
    private function getProducts(): array
    {
        $products = $this->coffeeMachineService->getAvailableProducts();

        if (empty($products)) {
            throw new MissingProductsException("There are no available products");
        }

        return array_map(fn(Coffee $product) => $product->toArray(), $products);
    }

    private function listAvailableProducts(OutputInterface $output, array $products): void
    {
        (new Table($output))->setHeaders(static::PRODUCTS_TABLE_HEADER)->setRows(array_values($products))->render();
    }

    private function selectProduct(InputInterface $input, OutputInterface $output, array $products): string
    {
        $helper = $this->getHelper("question");
        $question = new ChoiceQuestion("What do you want to drink today?", array_column($products, "name"));
        $question->setErrorMessage("We do not have this type of product. Please select another one");
        $product = $helper->ask($input, $output, $question);
        $output->writeln("Good choice");

        return trim($product);
    }

    private function selectQuantity(
        InputInterface $input,
        OutputInterface $output,
        array $products,
        string $product
    ): int {
        $helper = $this->getHelper("question");
        $question = new Question("Select quantity: ", 1);
        $question->setValidator(function ($quantity) use ($products, $product, $output) {
            $min = QuantityRange::MIN->value;
            $max = QuantityRange::MAX->value;

            if (!is_numeric($quantity) || (float)$quantity <= $min || (float)$quantity > $max) {
                throw new InvalidQuantityException(
                    sprintf(
                        "This is not a valid quantity. Only numeric values between %d and %d are allowed",
                        $min,
                        $max
                    )
                );
            }

            $availability = 0;

            foreach ($products as $productInfo) {
                if ($productInfo["name"] === $product) {
                    $availability = $productInfo["availability"];

                    break;
                }
            }

            if ($quantity > $availability) {
                throw new InvalidQuantityException(
                    sprintf("There are only %d remaining products", $availability)
                );
            }

            foreach ($products as $productInfo) {
                if ($productInfo["name"] === $product) {
                    $output->writeln(
                        sprintf(
                            "You have to pay %d euros for %d %s(s). Please insert the amount",
                            $productInfo["price"] * $quantity,
                            $quantity,
                            $productInfo["name"]
                        )
                    );

                    break;
                }
            }

            return (int)$quantity;
        });

        return $helper->ask($input, $output, $question);
    }

    private function pay(InputInterface $input, OutputInterface $output, string $productName, int $quantity): void
    {
        $question = new Question("Enter the amount to pay: ");

        $question->setValidator(function ($amount) use ($productName, $output, $quantity) {
            $change = $this->paymentService->calculatePrice($productName, $amount, $quantity);

            if (!empty($change)) {
                $output->writeln(sprintf("The change for your payment is %d", $change));
            }

            $output->writeln("Thank you!");

            return (int)$amount;
        });

        $this->getHelper("question")->ask($input, $output, $question);
    }
}