<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250902011507 extends AbstractMigration
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
        $this->addSql('ALTER TABLE product_sale DROP CONSTRAINT fk_68a3e2a44584665a');
        $this->addSql('ALTER TABLE product_sale DROP CONSTRAINT fk_68a3e2a44a7e4868');
        $this->addSql('DROP TABLE product_sale');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE product_sale (product_id INT NOT NULL, sale_id INT NOT NULL, PRIMARY KEY(product_id, sale_id))');
        $this->addSql('CREATE INDEX idx_68a3e2a44584665a ON product_sale (product_id)');
        $this->addSql('CREATE INDEX idx_68a3e2a44a7e4868 ON product_sale (sale_id)');
        $this->addSql('ALTER TABLE product_sale ADD CONSTRAINT fk_68a3e2a44584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_sale ADD CONSTRAINT fk_68a3e2a44a7e4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT FK_A654C63F4A7E4868');
        $this->addSql('ALTER TABLE sale_product DROP CONSTRAINT FK_A654C63F4584665A');
        $this->addSql('DROP TABLE sale_product');
    }
}
