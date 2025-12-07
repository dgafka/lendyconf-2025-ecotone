<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241130120002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create customers table with 5 seed customers';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE customers (
            customer_id UUID PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL
        )');

        // Seed 5 customers
        $customers = [
            ['550e8400-e29b-41d4-a716-446655440101', 'Alice Johnson', 'alice.johnson@example.com'],
            ['550e8400-e29b-41d4-a716-446655440102', 'Bob Smith', 'bob.smith@example.com'],
            ['550e8400-e29b-41d4-a716-446655440103', 'Carol Williams', 'carol.williams@example.com'],
            ['550e8400-e29b-41d4-a716-446655440104', 'David Brown', 'david.brown@example.com'],
            ['550e8400-e29b-41d4-a716-446655440105', 'Emma Davis', 'emma.davis@example.com'],
        ];

        foreach ($customers as [$id, $name, $email]) {
            $this->addSql(
                "INSERT INTO customers (customer_id, name, email) VALUES (:id, :name, :email)",
                ['id' => $id, 'name' => $name, 'email' => $email]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE customers');
    }
}

