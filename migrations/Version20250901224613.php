<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250901224613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_sale (product_id INT NOT NULL, sale_id INT NOT NULL, PRIMARY KEY(product_id, sale_id))');
        $this->addSql('CREATE INDEX IDX_68A3E2A44584665A ON product_sale (product_id)');
        $this->addSql('CREATE INDEX IDX_68A3E2A44A7E4868 ON product_sale (sale_id)');
        $this->addSql('CREATE TABLE sale (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN sale.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN sale.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE product_sale ADD CONSTRAINT FK_68A3E2A44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_sale ADD CONSTRAINT FK_68A3E2A44A7E4868 FOREIGN KEY (sale_id) REFERENCES sale (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_sale DROP CONSTRAINT FK_68A3E2A44584665A');
        $this->addSql('ALTER TABLE product_sale DROP CONSTRAINT FK_68A3E2A44A7E4868');
        $this->addSql('DROP TABLE product_sale');
        $this->addSql('DROP TABLE sale');
    }
}
