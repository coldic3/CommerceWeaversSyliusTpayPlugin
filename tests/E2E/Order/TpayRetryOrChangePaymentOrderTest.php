<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Order;

use Facebook\WebDriver\WebDriverBy;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\E2ETestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Account\LoginShopUserTrait;

final class TpayRetryOrChangePaymentOrderTest extends E2ETestCase
{
    use LoginShopUserTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_it_retries_payment_using_blik(): void
    {
        $this->loadFixtures(['blik_unpaid_order.yaml']);

        $this->loginShopUser('tony@nonexisting.cw', 'sylius');

        $this->client->get('/en_US/order/tokenValue1');
        $this->client->findElement(WebDriverBy::id('sylius_checkout_select_payment_payments_0_tpay_blik_token'))->sendKeys('777123');
        $this->client->submitForm('Pay');

        $this->assertPageTitleContains('Waiting for payment');
    }

    public function test_it_changes_payment_to_blik(): void
    {
        $this->loadFixtures(['card_unpaid_order.yaml']);

        $this->loginShopUser('tony@nonexisting.cw', 'sylius');

        $this->client->get('/en_US/order/tokenValue1');
        $form = $this->client->getCrawler()->selectButton('Pay')->form();
        $form->getElement()->findElement(WebDriverBy::xpath("//label[contains(text(),'BLIK (Tpay)')]"))->click();
        $this->client->findElement(WebDriverBy::id('sylius_checkout_select_payment_payments_0_tpay_blik_token'))->sendKeys('777123');
        $this->client->submitForm('Pay');

        $this->assertPageTitleContains('Waiting for payment');
    }
}
