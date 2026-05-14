<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514044813 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis ADD metier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BED16FA20 FOREIGN KEY (metier_id) REFERENCES activity (id)');
        $this->addSql('CREATE INDEX IDX_8B27C52BED16FA20 ON devis (metier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BED16FA20');
        $this->addSql('DROP INDEX IDX_8B27C52BED16FA20 ON devis');
        $this->addSql('ALTER TABLE devis DROP metier_id');
    }
}
