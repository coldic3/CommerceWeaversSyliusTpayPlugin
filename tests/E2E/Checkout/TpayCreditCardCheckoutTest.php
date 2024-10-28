<?php

declare(strict_types=1);

namespace E2E\Checkout;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\E2ETestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Account\LoginShopUserTrait;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Order\CartTrait;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Order\TpayTrait;

final class TpayCreditCardCheckoutTest extends E2ETestCase
{
    use CartTrait;
    use TpayTrait;
    use LoginShopUserTrait;

    private const FORM_ID = 'sylius_checkout_complete';

    private const CARD_NUMBER = '4012 0010 3714 1112';

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures(['addressed_cart.yaml']);

        // the cart is already addressed, so we go straight to selecting a shipping method
        $this->showSelectingShippingMethodStep();
        $this->processWithDefaultShippingMethod();
    }

    public function test_it_completes_the_checkout_using_credit_card(): void
    {
        $this->loginShopUser('tony@nonexisting.cw', 'sylius');

        $this->processWithPaymentMethod('tpay_card');
        $this->fillCardData(self::FORM_ID, self::CARD_NUMBER, '123', '01', '2029');
        $this->placeOrder();

        $this->assertPageTitleContains('Thank you!');
    }

    public function test_it_completes_the_checkout_using_credit_card_and_saves_the_card(): void
    {
        $this->loginShopUser('tony@nonexisting.cw', 'sylius');

        $this->processWithPaymentMethod('tpay_card');
        $this->fillCardData(self::FORM_ID, self::CARD_NUMBER, '123', '01', '2029', true);
        $this->placeOrder();

        $this->assertPageTitleContains('Thank you!');
    }

    public function test_it_forbids_card_saving_for_not_logged_in_users(): void
    {
        $this->expectException(NoSuchElementException::class);

        $this->processWithPaymentMethod('tpay_card');
        $this->fillCardData(self::FORM_ID, self::CARD_NUMBER, '123', '01', '2029', true);
    }
}
