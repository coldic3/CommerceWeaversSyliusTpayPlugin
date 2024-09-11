<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Factory;

use Sylius\Component\Core\Model\PaymentInterface;

interface CreateBlik0PaymentPayloadFactoryInterface
{
    /**
     * @return array<string, mixed>
     */
    public function createFrom(PaymentInterface $payment, string $notifyUrl, string $localeCode): array;
}
