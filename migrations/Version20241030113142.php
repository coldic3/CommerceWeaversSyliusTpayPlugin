<?php

declare(strict_types=1);

namespace CommerceWeaversSyliusTpayMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241030113142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias CHANGE registered registered TINYINT(1) DEFAULT false NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias CHANGE registered registered TINYINT(1) DEFAULT 0 NOT NULL');
    }
}
