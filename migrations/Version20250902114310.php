<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902114310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sales_products (sale_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(sale_id, product_id))');
        $this->addSql('CREATE INDEX IDX_FE06C27E4A7E4868 ON sales_products (sale_id)');
        $this->addSql('CREATE INDEX IDX_FE06C27E4584665A ON sales_products (product_id)');
        $this->addSql('ALTER TABLE sales_products ADD CONSTRAINT FK_FE06C27E4A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sales_products ADD CONSTRAINT FK_FE06C27E4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT fk_a654c63f4584665a');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT fk_a654c63f4a7e4868');
        $this->addSql('DROP TABLE sale_product');
        $this->addSql('ALTER TABLE product ALTER category_id DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE sale_product (sale_id INT NOT NULL, product_id INT NOT NULL, PRIMARY KEY(sale_id, product_id))');
        $this->addSql('CREATE INDEX idx_a654c63f4584665a ON sale_product (product_id)');
        $this->addSql('CREATE INDEX idx_a654c63f4a7e4868 ON sale_product (sale_id)');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT fk_a654c63f4584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_product ADD CONSTRAINT fk_a654c63f4a7e4868 FOREIGN KEY (sale_id) REFERENCES sale (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sales_products DROP CONSTRAINT FK_FE06C27E4A7E4868');
        $this->addSql('ALTER TABLE sales_products DROP CONSTRAINT FK_FE06C27E4584665A');
        $this->addSql('DROP TABLE sales_products');
        $this->addSql('ALTER TABLE product ALTER category_id SET NOT NULL');
    }
}
