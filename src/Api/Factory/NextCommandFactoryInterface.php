<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Factory;

use CommerceWeavers\SyliusTpayPlugin\Api\Command\Pay;
use Sylius\Component\Core\Model\PaymentInterface;

interface NextCommandFactoryInterface
{
    public function create(Pay $command, PaymentInterface $payment): object;

    public function supports(Pay $command, PaymentInterface $payment): bool;
}
