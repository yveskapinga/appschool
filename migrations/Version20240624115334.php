<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624115334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE matiere CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE paiement CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE matiere CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE paiement CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
