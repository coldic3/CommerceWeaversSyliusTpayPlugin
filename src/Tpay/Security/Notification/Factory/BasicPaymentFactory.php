<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Security\Notification\Factory;

use Tpay\OpenApi\Utilities\Util;
use tpaySDK\Model\Objects\NotificationBody\BasicPayment;

final class BasicPaymentFactory implements BasicPaymentFactoryInterface
{
    public function createFromArray(array $data): BasicPayment
    {
        $paymentData = new BasicPayment();

        foreach ($data as $key => $value) {
            $this->setPaymentData($paymentData, $key, $value);
        }

        return $paymentData;
    }

    private function setPaymentData(BasicPayment $paymentData, string $key, mixed $value): void
    {
        if (property_exists($paymentData, $key)) {
            $paymentData->{$key} = Util::cast($value, $paymentData->{$key}->getType());
        }
    }
}
