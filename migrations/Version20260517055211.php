<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260517055211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_artisan CHANGE send_at send_at DATETIME DEFAULT NULL, CHANGE view_at view_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE testimonial CHANGE review review DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis_artisan CHANGE send_at send_at DATETIME NOT NULL, CHANGE view_at view_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE testimonial CHANGE review review VARCHAR(255) NOT NULL');
    }
}
