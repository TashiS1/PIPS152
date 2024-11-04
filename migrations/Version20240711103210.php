<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240711103210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_emprunt (id INT AUTO_INCREMENT NOT NULL, materiel_id INT NOT NULL, emprunteur VARCHAR(255) NOT NULL, date_emprunt DATETIME NOT NULL, date_retour DATETIME DEFAULT NULL, INDEX IDX_54542EA816880AAF (materiel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE historique_emprunt ADD CONSTRAINT FK_54542EA816880AAF FOREIGN KEY (materiel_id) REFERENCES materiel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE historique_emprunt DROP FOREIGN KEY FK_54542EA816880AAF');
        $this->addSql('DROP TABLE historique_emprunt');
    }
}
