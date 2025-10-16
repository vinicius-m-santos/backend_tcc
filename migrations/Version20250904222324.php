<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250904222324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expense ADD total DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('UPDATE expense SET total = 0 WHERE total IS NULL');
        $this->addSql('ALTER TABLE expense ALTER COLUMN total SET DEFAULT 0');
        $this->addSql('ALTER TABLE expense ALTER COLUMN total SET NOT NULL');
        $this->addSql('ALTER TABLE sale ALTER total DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE expense DROP total');
        $this->addSql('ALTER TABLE sale ALTER total SET DEFAULT \'0\'');
    }
}
