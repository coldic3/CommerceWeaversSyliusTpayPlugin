<?php

declare(strict_types=1);

namespace E2E\RetryPayment;

use Tests\CommerceWeavers\SyliusTpayPlugin\Api\Utils\OrderPlacerTrait;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\E2ETestCase;
use Tests\CommerceWeavers\SyliusTpayPlugin\E2E\Helper\Order\RetryPaymentTrait;

final class RetryingPaymentTest extends E2ETestCase
{
    use OrderPlacerTrait;
    use RetryPaymentTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpOrderPlacer();

        $this->loadFixtures([
            'addressed_cart.yaml',
            'channel.yaml',
            'country.yaml',
            'customer.yaml',
            'payment_method.yaml',
            'shipping_category.yaml',
            'shipping_method.yaml',
            'tax_category.yaml',
        ]);
    }

    public function test_it_retries_payment(): void
    {
        $this->doPlaceOrder('t0k3n', productVariantCode: 'MUG');

        $this->showPaymentFailedPage('t0k3n');
        $this->retryPayment();

        $this->assertPageTitleContains('Summary of your order');
        $this->assertSelectorWillContain('.sylius-flash-message', 'The previous payment has been cancelled');
    }
}
