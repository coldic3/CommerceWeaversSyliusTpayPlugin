<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusTpayPlugin\Unit\Tpay\Factory;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateInitializeApplePayPaymentPayloadFactory;
use CommerceWeavers\SyliusTpayPlugin\Tpay\Factory\CreateInitializeApplePayPaymentPayloadFactoryInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use PHPUnit\Framework\TestCase;

final class CreateInitializeApplePayPaymentPayloadFactoryTest extends TestCase
{
    public function test_it_creates_a_payload_from_a_model(): void
    {
        $model = new ArrayObject([
            'domainName' => 'example.com',
            'displayName' => 'Example',
            'validationUrl' => 'https://example.com/apple-pay-validation-url',
        ]);

        $result = $this->createTestSubject()->create($model);

        $this->assertSame([
            'domainName' => 'example.com',
            'displayName' => 'Example',
            'validationUrl' => 'https://example.com/apple-pay-validation-url',
        ], $result);
    }

    private function createTestSubject(): CreateInitializeApplePayPaymentPayloadFactoryInterface
    {
        return new CreateInitializeApplePayPaymentPayloadFactory();
    }
}
