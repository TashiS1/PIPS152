<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240716094714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3E522A506');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3E522A506 FOREIGN KEY (permanent_id) REFERENCES permanent (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3E522A506');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3E522A506 FOREIGN KEY (permanent_id) REFERENCES permanent (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
