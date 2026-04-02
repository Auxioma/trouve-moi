<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260402105145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, amount NUMERIC(10, 2) NOT NULL, currency VARCHAR(10) NOT NULL, status VARCHAR(30) NOT NULL, provider_payment_id VARCHAR(255) DEFAULT NULL, paid_at DATETIME DEFAULT NULL, subscription_id INT NOT NULL, INDEX IDX_6D28840D9A1887DC (subscription_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(50) NOT NULL, price_monthly NUMERIC(10, 2) NOT NULL, price_yearly NUMERIC(10, 2) DEFAULT NULL, is_active TINYINT NOT NULL, features JSON DEFAULT NULL, UNIQUE INDEX UNIQ_DD5A5B7D77153098 (code), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(30) NOT NULL, billing_cycle VARCHAR(20) NOT NULL, started_at DATETIME DEFAULT NULL, ends_at DATETIME DEFAULT NULL, canceled_at DATETIME DEFAULT NULL, auto_renew TINYINT DEFAULT 0 NOT NULL, provider VARCHAR(255) DEFAULT NULL, provider_subscription_id VARCHAR(255) DEFAULT NULL, provider_customer_id VARCHAR(255) DEFAULT NULL, user_id INT NOT NULL, plan_id INT NOT NULL, INDEX IDX_A3C664D3A76ED395 (user_id), INDEX IDX_A3C664D3E899029B (plan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D9A1887DC');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3E899029B');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE plan');
        $this->addSql('DROP TABLE subscription');
    }
}
