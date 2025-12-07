<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241130120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create orders table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE orders (
            order_id VARCHAR(36) NOT NULL PRIMARY KEY,
            product_ids JSON NOT NULL,
            customer_id VARCHAR(36) NOT NULL,
            status VARCHAR(20) NOT NULL,
            total_price_amount INT NOT NULL,
            total_price_currency VARCHAR(3) NOT NULL,
            created_at TIMESTAMP NOT NULL
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE orders');
    }
}

