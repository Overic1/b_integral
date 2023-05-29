<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230526155237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise CHANGE nom_de_la_base nom_de_la_base VARCHAR(255) DEFAULT NULL, CHANGE user user VARCHAR(255) DEFAULT NULL, CHANGE pass_base pass_base VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise CHANGE nom_de_la_base nom_de_la_base VARCHAR(255) NOT NULL, CHANGE user user VARCHAR(255) NOT NULL, CHANGE pass_base pass_base VARCHAR(255) NOT NULL');
    }
}
