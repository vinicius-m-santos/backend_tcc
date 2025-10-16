<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902120245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sale_product (sale_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(sale_id, product_id))');
        $this->addSql('CREATE INDEX IDX_A654C63F4A7E4868 ON sale_product (sale_id)');
        $this->addSql('CREATE INDEX IDX_A654C63F4584665A ON sale_product (product_id)');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT FK_A654C63F4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT FK_A654C63F4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sales_products DROP CONSTRAINT fk_fe06c27e4584665a');
        $this->addSql('ALTER TABLE sales_products DROP CONSTRAINT fk_fe06c27e4a7e4868');
        $this->addSql('DROP TABLE sales_products');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE sales_products (sale_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(sale_id, product_id))');
        $this->addSql('CREATE INDEX idx_fe06c27e4584665a ON sales_products (product_id)');
        $this->addSql('CREATE INDEX idx_fe06c27e4a7e4868 ON sales_products (sale_id)');
        $this->addSql('ALTER TABLE sales_products ADD CONSTRAINT fk_fe06c27e4584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sales_products ADD CONSTRAINT fk_fe06c27e4a7e4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT FK_A654C63F4A7E4868');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT FK_A654C63F4584665A');
        $this->addSql('DROP TABLE sale_product');
    }
}
