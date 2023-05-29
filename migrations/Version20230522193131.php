<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522193131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise CHANGE base_url base_url VARCHAR(255) DEFAULT NULL, CHANGE api_key api_key VARCHAR(255) DEFAULT NULL, CHANGE num_tel num_tel INT DEFAULT NULL, CHANGE num_ifu num_ifu INT DEFAULT NULL, CHANGE num_nim num_nim INT DEFAULT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entreprise CHANGE base_url base_url VARCHAR(255) NOT NULL, CHANGE api_key api_key VARCHAR(255) NOT NULL, CHANGE logo logo VARCHAR(255) NOT NULL, CHANGE num_tel num_tel INT NOT NULL, CHANGE num_ifu num_ifu INT NOT NULL, CHANGE num_nim num_nim INT NOT NULL');
    }
}
