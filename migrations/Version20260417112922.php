<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260417112922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE conversation_participant (id INT AUTO_INCREMENT NOT NULL, joined_at DATETIME NOT NULL, conversation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_398016619AC0396 (conversation_id), INDEX IDX_39801661A76ED395 (user_id), UNIQUE INDEX uniq_conversation_user (conversation_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, conversation_id INT NOT NULL, sender_id INT NOT NULL, INDEX IDX_B6BD307F9AC0396 (conversation_id), INDEX IDX_B6BD307FF624B39D (sender_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message_read (id INT AUTO_INCREMENT NOT NULL, read_at DATETIME NOT NULL, message_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_31C2DABE537A1329 (message_id), INDEX IDX_31C2DABEA76ED395 (user_id), UNIQUE INDEX uniq_message_user_read (message_id, user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(30) NOT NULL, provider_payment_id VARCHAR(255) DEFAULT NULL, paid_at DATETIME DEFAULT NULL, subscription_id INT NOT NULL, INDEX IDX_6D28840D9A1887DC (subscription_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_8F7C2FC0A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(50) NOT NULL, price_monthly NUMERIC(10, 2) NOT NULL, price_yearly NUMERIC(10, 2) DEFAULT NULL, is_active TINYINT NOT NULL, features JSON DEFAULT NULL, UNIQUE INDEX UNIQ_DD5A5B7D77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote (id INT AUTO_INCREMENT NOT NULL, reference VARCHAR(50) NOT NULL, type VARCHAR(50) NOT NULL, status VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, client_name VARCHAR(255) NOT NULL, client_email VARCHAR(255) NOT NULL, client_phone VARCHAR(50) DEFAULT NULL, client_address VARCHAR(255) DEFAULT NULL, quote_date DATE NOT NULL, valid_until DATE DEFAULT NULL, subtotal_ht NUMERIC(10, 2) DEFAULT NULL, tva_rate NUMERIC(5, 2) DEFAULT NULL, tva_amount NUMERIC(10, 2) DEFAULT NULL, total_ttc NUMERIC(10, 2) DEFAULT NULL, execution_time VARCHAR(255) DEFAULT NULL, payment_terms VARCHAR(255) DEFAULT NULL, legal_notes LONGTEXT DEFAULT NULL, message LONGTEXT DEFAULT NULL, is_pdf_uploaded TINYINT NOT NULL, is_pdf_generated TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, opened_at DATETIME DEFAULT NULL, accepted_at DATETIME DEFAULT NULL, paid_at DATETIME DEFAULT NULL, artisan_id INT NOT NULL, client_user_id INT DEFAULT NULL, conversation_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_6B71CBF4AEA34913 (reference), INDEX IDX_6B71CBF45ED3C7B7 (artisan_id), INDEX IDX_6B71CBF4F55397E8 (client_user_id), INDEX IDX_6B71CBF49AC0396 (conversation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_file (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, original_name VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, file_path VARCHAR(255) DEFAULT NULL, file_size INT DEFAULT NULL, mime_type VARCHAR(100) DEFAULT NULL, created_at DATETIME NOT NULL, quote_id INT NOT NULL, INDEX IDX_1E78699ADB805178 (quote_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE quote_item (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, quantity NUMERIC(10, 2) DEFAULT NULL, unit VARCHAR(50) DEFAULT NULL, unit_price_ht NUMERIC(10, 2) DEFAULT NULL, total_ht NUMERIC(10, 2) DEFAULT NULL, position INT NOT NULL, total_ttc NUMERIC(10, 2) NOT NULL, quote_id INT NOT NULL, INDEX IDX_8DFC7A94DB805178 (quote_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, activity_id INT DEFAULT NULL, INDEX IDX_7332E16981C06096 (activity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(30) NOT NULL, billing_cycle VARCHAR(20) NOT NULL, started_at DATETIME DEFAULT NULL, ends_at DATETIME DEFAULT NULL, canceled_at DATETIME DEFAULT NULL, auto_renew TINYINT DEFAULT 0 NOT NULL, provider VARCHAR(255) DEFAULT NULL, provider_subscription_id VARCHAR(255) DEFAULT NULL, provider_customer_id VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, plan_id INT NOT NULL, INDEX IDX_A3C664D3A76ED395 (user_id), INDEX IDX_A3C664D3E899029B (plan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE testimonial (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT NOT NULL, review VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, author_id INT NOT NULL, artisan_id INT NOT NULL, INDEX IDX_E6BDCDF7F675F31B (author_id), INDEX IDX_E6BDCDF75ED3C7B7 (artisan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, compagny VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, siren VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, latitude VARCHAR(255) DEFAULT NULL, longitude VARCHAR(255) DEFAULT NULL, profile_status VARCHAR(255) DEFAULT \'profil_partiel\' NOT NULL, description LONGTEXT DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, logo_size INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, slug VARCHAR(255) NOT NULL, grande_description LONGTEXT DEFAULT NULL, last_login DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, siret VARCHAR(255) NOT NULL, activity_id INT DEFAULT NULL, INDEX IDX_8D93D64981C06096 (activity_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_services (user_id INT NOT NULL, services_id INT NOT NULL, INDEX IDX_93BF0569A76ED395 (user_id), INDEX IDX_93BF0569AEF5A6C1 (services_id), PRIMARY KEY (user_id, services_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE conversation_participant ADD CONSTRAINT FK_398016619AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE conversation_participant ADD CONSTRAINT FK_39801661A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_read ADD CONSTRAINT FK_31C2DABE537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_read ADD CONSTRAINT FK_31C2DABEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF45ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4F55397E8 FOREIGN KEY (client_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF49AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)');
        $this->addSql('ALTER TABLE quote_file ADD CONSTRAINT FK_1E78699ADB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE quote_item ADD CONSTRAINT FK_8DFC7A94DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE services ADD CONSTRAINT FK_7332E16981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF7F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF75ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user_services ADD CONSTRAINT FK_93BF0569A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_services ADD CONSTRAINT FK_93BF0569AEF5A6C1 FOREIGN KEY (services_id) REFERENCES services (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE conversation_participant DROP FOREIGN KEY FK_398016619AC0396');
        $this->addSql('ALTER TABLE conversation_participant DROP FOREIGN KEY FK_39801661A76ED395');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message_read DROP FOREIGN KEY FK_31C2DABE537A1329');
        $this->addSql('ALTER TABLE message_read DROP FOREIGN KEY FK_31C2DABEA76ED395');
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9A1887DC');
        $this->addSql('ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0A76ED395');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF45ED3C7B7');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF4F55397E8');
        $this->addSql('ALTER TABLE quote DROP FOREIGN KEY FK_6B71CBF49AC0396');
        $this->addSql('ALTER TABLE quote_file DROP FOREIGN KEY FK_1E78699ADB805178');
        $this->addSql('ALTER TABLE quote_item DROP FOREIGN KEY FK_8DFC7A94DB805178');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE services DROP FOREIGN KEY FK_7332E16981C06096');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3E899029B');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF7F675F31B');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF75ED3C7B7');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64981C06096');
        $this->addSql('ALTER TABLE user_services DROP FOREIGN KEY FK_93BF0569A76ED395');
        $this->addSql('ALTER TABLE user_services DROP FOREIGN KEY FK_93BF0569AEF5A6C1');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE conversation');
        $this->addSql('DROP TABLE conversation_participant');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_read');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE pictures');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE quote');
        $this->addSql('DROP TABLE quote_file');
        $this->addSql('DROP TABLE quote_item');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE services');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE testimonial');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_services');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
