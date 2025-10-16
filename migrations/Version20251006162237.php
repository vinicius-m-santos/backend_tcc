<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251006162237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD cpf VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE "user" ADD phone_number VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE "user" ADD state VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE "user" ADD city VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE "user" ADD dob DATE NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP cpf');
        $this->addSql('ALTER TABLE "user" DROP phone_number');
        $this->addSql('ALTER TABLE "user" DROP state');
        $this->addSql('ALTER TABLE "user" DROP city');
        $this->addSql('ALTER TABLE "user" DROP dob');
    }
}
