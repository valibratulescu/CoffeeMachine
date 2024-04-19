# CoffeeMachine

##### Author: Valentin Bratulescu

## Prerequisites (for Windows)

- Install PHP
- Install Git Bash and Git CLI
- Install MySQL server
- Install Composer

## Requirements

- PHP >=8.2
- Symfony 7.0

## Setup

```bat
$ composer install
$ symfony check:requirements
```

## Plot

Implement a CLI-based application that emulates a coffee machine. The software should meet the following requirements:
- it presents an interface that allows the visitor to:
  - list available products (id, name, price, content, availability/quantity)
  - select one of them by inputing product ID and quantity
  - receive payment for order, by cash or card
- once a customer starts using the vending machine, the machine is locked for other customers until the customer leaves (if we run the command from another terminal, it won't let us use the machine)
- the machine keeps track of products in a database
- the machine keeps a history of transactions in a database
- for payments, the machine keeps track of available monetary units in when accepts cash and returns the change
- the products inventory is managed through the available quantities of the necessary ingredients (e.q. 1 x 40ml espresso is actually (5g coffee, 2g sugar, 0g milk, 30ml water)

## Output Example

+-----------------+-------+--------------+-----------------------------------+
| Name            | Price | Availability | Content                           |
+-----------------+-------+--------------+-----------------------------------+
| Coffee          | 3     | 7            | milk:0,sugar:0,water:40,coffee:6  |
| Espresso        | 6     | 5            | milk:0,sugar:0,water:25,coffee:10 |
| Cappuccino      | 8     | 3            | milk:10,sugar:7,water:65,coffee:3 |
| Latte Macchiato | 8     | 3            | milk:15,sugar:8,water:80,coffee:4 |
+-----------------+-------+--------------+-----------------------------------+
What do you want to drink today?
[0] Coffee
[1] Espresso
[2] Cappuccino
[3] Latte Macchiato
> 0
0
Good choice
Select quantity: 4
You have to pay 12 euros for 4 Coffee(s). Please insert the amount
Enter the amount to pay: 7
The amount you payed is not enough. You should pay 12 euros
Enter the amount to pay: 25
The change for your payment is 13
Thank you!

[OK] Your Coffees are ready. Enjoy!