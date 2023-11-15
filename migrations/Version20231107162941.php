<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231107162941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE emplois (id INT AUTO_INCREMENT NOT NULL, personne_id INT DEFAULT NULL, nom_entreprise VARCHAR(255) NOT NULL, post VARCHAR(255) NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, INDEX IDX_461274B9A21BD112 (personne_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personnes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, naissance DATE NOT NULL, age INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE emplois ADD CONSTRAINT FK_461274B9A21BD112 FOREIGN KEY (personne_id) REFERENCES personnes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emplois DROP FOREIGN KEY FK_461274B9A21BD112');
        $this->addSql('DROP TABLE emplois');
        $this->addSql('DROP TABLE personnes');
    }
}
