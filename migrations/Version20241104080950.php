<?php

declare(strict_types=1);

namespace CommerceWeaversSyliusTpayMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241104080950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add BlikAlias::channel relation.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias ADD channel_id INT NOT NULL');
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias ADD CONSTRAINT FK_72E078BE72F5A1AA FOREIGN KEY (channel_id) REFERENCES sylius_channel (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_72E078BE72F5A1AA ON cw_sylius_tpay_blik_alias (channel_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias DROP FOREIGN KEY FK_72E078BE72F5A1AA');
        $this->addSql('DROP INDEX IDX_72E078BE72F5A1AA ON cw_sylius_tpay_blik_alias');
        $this->addSql('ALTER TABLE cw_sylius_tpay_blik_alias DROP channel_id');
    }
}
