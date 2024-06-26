<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240624103742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, professeur_principal_id INT DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, niveau VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8F87BF961BF3D36A (professeur_principal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE matiere (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, coefficient DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement (id INT AUTO_INCREMENT NOT NULL, eleve_id INT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, mode_paiement VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B1DC7A1EA6CC7B2 (eleve_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, classe_id INT DEFAULT NULL, parents_id INT DEFAULT NULL, email VARCHAR(180) DEFAULT NULL, roles JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) DEFAULT NULL, prenom VARCHAR(50) DEFAULT NULL, nom VARCHAR(50) DEFAULT NULL, date_de_naissance DATE DEFAULT NULL, sexe VARCHAR(1) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, numero_telephone VARCHAR(50) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, identification_nationale VARCHAR(255) DEFAULT NULL, fonction VARCHAR(50) DEFAULT NULL, INDEX IDX_1D1C63B38F5EA509 (classe_id), INDEX IDX_1D1C63B3B706B6D3 (parents_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignant_matiere (enseignant_id INT NOT NULL, matiere_id INT NOT NULL, INDEX IDX_33D1A024E455FCC0 (enseignant_id), INDEX IDX_33D1A024F46CD258 (matiere_id), PRIMARY KEY(enseignant_id, matiere_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignant_classe (enseignant_id INT NOT NULL, classe_id INT NOT NULL, INDEX IDX_F670A5F4E455FCC0 (enseignant_id), INDEX IDX_F670A5F48F5EA509 (classe_id), PRIMARY KEY(enseignant_id, classe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF961BF3D36A FOREIGN KEY (professeur_principal_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EA6CC7B2 FOREIGN KEY (eleve_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B38F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3B706B6D3 FOREIGN KEY (parents_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE enseignant_matiere ADD CONSTRAINT FK_33D1A024E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enseignant_matiere ADD CONSTRAINT FK_33D1A024F46CD258 FOREIGN KEY (matiere_id) REFERENCES matiere (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enseignant_classe ADD CONSTRAINT FK_F670A5F4E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enseignant_classe ADD CONSTRAINT FK_F670A5F48F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF961BF3D36A');
        $this->addSql('ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EA6CC7B2');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B38F5EA509');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3B706B6D3');
        $this->addSql('ALTER TABLE enseignant_matiere DROP FOREIGN KEY FK_33D1A024E455FCC0');
        $this->addSql('ALTER TABLE enseignant_matiere DROP FOREIGN KEY FK_33D1A024F46CD258');
        $this->addSql('ALTER TABLE enseignant_classe DROP FOREIGN KEY FK_F670A5F4E455FCC0');
        $this->addSql('ALTER TABLE enseignant_classe DROP FOREIGN KEY FK_F670A5F48F5EA509');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE matiere');
        $this->addSql('DROP TABLE paiement');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE enseignant_matiere');
        $this->addSql('DROP TABLE enseignant_classe');
    }
}
