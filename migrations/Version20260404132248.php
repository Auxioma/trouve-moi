<?php

declare(strict_types=1);

/**
 * Copyright (c) 2026 Auxioma Web Agency
 * https://trouvemoi.eu
 *
 * Ce fichier fait partie du projet Trouvemoi.eu développé par Auxioma Web Agency.
 * Tous droits réservés.
 *
 * Ce code source, son architecture, sa structure, ses scripts et ses composants
 * sont la propriété exclusive de Auxioma Web Agency et de ses partenaires.
 *
 * Toute reproduction, modification, distribution, publication ou utilisation,
 * totale ou partielle, sans autorisation écrite préalable est strictement interdite.
 *
 * Ce code est confidentiel et propriétaire.
 * Droit applicable : Monde.
 */

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404132248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, client_name VARCHAR(255) NOT NULL, client_email VARCHAR(255) NOT NULL, client_phone VARCHAR(50) DEFAULT NULL, client_address VARCHAR(255) DEFAULT NULL, quote_date DATE NOT NULL, valid_until DATE DEFAULT NULL, subtotal_ht NUMERIC(10, 2) DEFAULT NULL, tva_rate NUMERIC(5, 2) DEFAULT NULL, tva_amount NUMERIC(10, 2) DEFAULT NULL, total_ttc NUMERIC(10, 2) DEFAULT NULL, execution_time VARCHAR(255) DEFAULT NULL, payment_terms VARCHAR(255) DEFAULT NULL, legal_notes LONGTEXT DEFAULT NULL, message LONGTEXT DEFAULT NULL, is_pdf_uploaded TINYINT NOT NULL, is_pdf_generated TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, opened_at DATETIME DEFAULT NULL, accepted_at DATETIME DEFAULT NULL, paid_at DATETIME DEFAULT NULL, artisan_id INT NOT NULL, client_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6B71CBF4AEA34913 (reference), INDEX IDX_6B71CBF45ED3C7B7 (artisan_id), INDEX IDX_6B71CBF4F55397E8 (client_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_file (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, original_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, file_path VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, mime_type VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, quote_id INT NOT NULL, INDEX IDX_1E78699ADB805178 (quote_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_item (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, quantity NUMERIC(10, 2) DEFAULT NULL, unit VARCHAR(50) DEFAULT NULL, unit_price_ht NUMERIC(10, 2) DEFAULT NULL, total_ht NUMERIC(10, 2) DEFAULT NULL, position INT NOT NULL, quote_id INT NOT NULL, INDEX IDX_8DFC7A94DB805178 (quote_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF45ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4F55397E8 FOREIGN KEY (client_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote_file ADD CONSTRAINT FK_1E78699ADB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE quote_item ADD CONSTRAINT FK_8DFC7A94DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF45ED3C7B7');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4F55397E8');
        $this->addSql('ALTER TABLE quote_file DROP FOREIGN KEY FK_1E78699ADB805178');
        $this->addSql('ALTER TABLE quote_item DROP FOREIGN KEY FK_8DFC7A94DB805178');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_file');
        $this->addSql('DROP TABLE quote_item');
    }
}
