<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230605115646 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE installation_entreprise (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, installation_id INT NOT NULL, INDEX IDX_C0CC5185A4AEAFEA (entreprise_id), INDEX IDX_C0CC5185167B88B4 (installation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE installation_entreprise ADD CONSTRAINT FK_C0CC5185A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('ALTER TABLE installation_entreprise ADD CONSTRAINT FK_C0CC5185167B88B4 FOREIGN KEY (installation_id) REFERENCES installation (id)');
        $this->addSql('ALTER TABLE entreprise ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE installation_entreprise DROP FOREIGN KEY FK_C0CC5185A4AEAFEA');
        $this->addSql('ALTER TABLE installation_entreprise DROP FOREIGN KEY FK_C0CC5185167B88B4');
        $this->addSql('DROP TABLE installation_entreprise');
        $this->addSql('ALTER TABLE entreprise DROP roles');
    }
}
