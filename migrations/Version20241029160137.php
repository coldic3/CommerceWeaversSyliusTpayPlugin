<?php

declare(strict_types=1);

namespace CommerceWeaversSyliusTpayMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241029160137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Introduce CreditCard entity.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE cw_sylius_tpay_credt_card (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, token VARCHAR(255) NOT NULL, brand VARCHAR(255) NOT NULL, tail VARCHAR(255) NOT NULL, expiration_date DATETIME DEFAULT NULL, INDEX IDX_9FF1996C9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8 COLLATE `UTF8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cw_sylius_tpay_credt_card ADD CONSTRAINT FK_9FF1996C9395C3F3 FOREIGN KEY (customer_id) REFERENCES sylius_customer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cw_sylius_tpay_credt_card DROP FOREIGN KEY FK_9FF1996C9395C3F3');
        $this->addSql('DROP TABLE cw_sylius_tpay_credt_card');
    }
}
