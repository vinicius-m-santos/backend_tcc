<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904235555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense ALTER total DROP DEFAULT');
        $this->addSql('ALTER TABLE sale ADD quantity INT');
        $this->addSql('UPDATE sale SET quantity = 0 WHERE quantity IS NULL');
        $this->addSql('ALTER TABLE sale ALTER COLUMN quantity SET DEFAULT 0');
        $this->addSql('ALTER TABLE sale ALTER COLUMN quantity SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense ALTER total SET DEFAULT \'0\'');
        $this->addSql('ALTER TABLE sale DROP quantity');
    }
}
