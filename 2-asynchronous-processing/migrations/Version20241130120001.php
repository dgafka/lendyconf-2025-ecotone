<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241130120001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table and update orders to use order_lines';
    }

    public function up(Schema $schema): void
    {
        // Create products table
        $this->addSql('CREATE TABLE products (
            product_id VARCHAR(36) NOT NULL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price_amount INT NOT NULL,
            price_currency VARCHAR(3) NOT NULL DEFAULT \'USD\'
        )');

        // Update orders table: rename product_ids to order_lines
        $this->addSql('ALTER TABLE orders RENAME COLUMN product_ids TO order_lines');

        // Insert 10 electronics products
        $products = [
            ['id' => '550e8400-e29b-41d4-a716-446655440001', 'name' => 'Laptop Pro 15', 'description' => 'High-performance 15-inch laptop with Intel Core i7, 16GB RAM, 512GB SSD', 'price' => 129900],
            ['id' => '550e8400-e29b-41d4-a716-446655440002', 'name' => 'Smartphone X12', 'description' => '6.5-inch OLED display, 128GB storage, 48MP camera', 'price' => 89900],
            ['id' => '550e8400-e29b-41d4-a716-446655440003', 'name' => 'Wireless Headphones', 'description' => 'Noise-cancelling over-ear headphones with 30-hour battery life', 'price' => 29900],
            ['id' => '550e8400-e29b-41d4-a716-446655440004', 'name' => 'Tablet Air', 'description' => '10.9-inch tablet with M1 chip, 256GB storage', 'price' => 59900],
            ['id' => '550e8400-e29b-41d4-a716-446655440005', 'name' => 'Smart Watch Series 5', 'description' => 'Fitness tracking, heart rate monitor, GPS, water resistant', 'price' => 39900],
            ['id' => '550e8400-e29b-41d4-a716-446655440006', 'name' => '4K Monitor 27"', 'description' => '27-inch 4K UHD IPS monitor with USB-C connectivity', 'price' => 44900],
            ['id' => '550e8400-e29b-41d4-a716-446655440007', 'name' => 'Mechanical Keyboard', 'description' => 'RGB backlit mechanical keyboard with Cherry MX switches', 'price' => 14900],
            ['id' => '550e8400-e29b-41d4-a716-446655440008', 'name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse with 16000 DPI sensor', 'price' => 7900],
            ['id' => '550e8400-e29b-41d4-a716-446655440009', 'name' => 'USB-C Hub', 'description' => '7-in-1 USB-C hub with HDMI, USB 3.0, SD card reader', 'price' => 4900],
            ['id' => '550e8400-e29b-41d4-a716-446655440010', 'name' => 'Portable SSD 1TB', 'description' => '1TB portable SSD with USB 3.2, up to 1050MB/s transfer speed', 'price' => 11900],
        ];

        foreach ($products as $product) {
            $this->addSql(
                "INSERT INTO products (product_id, name, description, price_amount, price_currency) VALUES (:id, :name, :desc, :price, 'USD')",
                ['id' => $product['id'], 'name' => $product['name'], 'desc' => $product['description'], 'price' => $product['price']]
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE orders RENAME COLUMN order_lines TO product_ids');
        $this->addSql('DROP TABLE products');
    }
}

