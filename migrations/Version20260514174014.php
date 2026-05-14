<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514174014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis_artisan (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) NOT NULL, send_at DATETIME NOT NULL, view_at DATETIME NOT NULL, answer_at DATETIME NOT NULL, message LONGTEXT DEFAULT NULL, devis_id INT DEFAULT NULL, artisan_id INT DEFAULT NULL, INDEX IDX_AE8E918F41DEFADA (devis_id), INDEX IDX_AE8E918F5ED3C7B7 (artisan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE devis_artisan ADD CONSTRAINT FK_AE8E918F41DEFADA FOREIGN KEY (devis_id) REFERENCES devis (id)');
        $this->addSql('ALTER TABLE devis_artisan ADD CONSTRAINT FK_AE8E918F5ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES app_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_artisan DROP FOREIGN KEY FK_AE8E918F41DEFADA');
        $this->addSql('ALTER TABLE devis_artisan DROP FOREIGN KEY FK_AE8E918F5ED3C7B7');
        $this->addSql('DROP TABLE devis_artisan');
    }
}
