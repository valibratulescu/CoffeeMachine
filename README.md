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