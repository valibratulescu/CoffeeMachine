<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240419185021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredients (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, quantity INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_4B60114F5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, availability INT NOT NULL, content JSON NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B3BA5A5A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transactions (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, price DOUBLE PRECISION NOT NULL, created DATETIME NOT NULL, INDEX IDX_EAA81A4C4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql(
            <<<SQL
INSERT INTO `products` (`id`, `name`, `price`, `availability`, `content`, `type`) VALUES (1, 'Coffee', 3, 7, '{"water":40,"coffee":6,"sugar":0,"milk":0}', 'coffee');
INSERT INTO `products` (`id`, `name`, `price`, `availability`, `content`, `type`) VALUES (2, 'Espresso', 6, 5, '{"water":25,"coffee":10,"sugar":0,"milk":0}', 'espresso');
INSERT INTO `products` (`id`, `name`, `price`, `availability`, `content`, `type`) VALUES (3, 'Cappuccino', 8, 3, '{"water":65,"coffee":3,"sugar":7,"milk":10}', 'cappuccino');
INSERT INTO `products` (`id`, `name`, `price`, `availability`, `content`, `type`) VALUES (4, 'Latte Macchiato', 8, 3, '{"water":80,"coffee":4,"sugar":8,"milk":15}', 'lattemacchiato');
SQL
        );

        $this->addSql(
            <<<SQL
INSERT INTO `ingredients` (`id`, `name`, `quantity`) VALUES (1, 'water', 300);
INSERT INTO `ingredients` (`id`, `name`, `quantity`) VALUES (2, 'coffee', 250);
INSERT INTO `ingredients` (`id`, `name`, `quantity`) VALUES (3, 'milk', 280);
INSERT INTO `ingredients` (`id`, `name`, `quantity`) VALUES (4, 'sugar', 400);
SQL
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transactions DROP FOREIGN KEY FK_EAA81A4C4584665A');
        $this->addSql('DROP TABLE ingredients');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE transactions');
    }
}
