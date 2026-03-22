<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260321141521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE testimonial (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user ADD testimonial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6491D4EC6B1 FOREIGN KEY (testimonial_id) REFERENCES testimonial (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6491D4EC6B1 ON user (testimonial_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6491D4EC6B1');
        $this->addSql('DROP INDEX IDX_8D93D6491D4EC6B1 ON user');
        $this->addSql('ALTER TABLE user DROP testimonial_id');
    }
}
