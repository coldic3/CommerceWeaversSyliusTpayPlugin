<?php

declare(strict_types=1);

namespace App\Repository;

use CommerceWeavers\SyliusTpayPlugin\Repository\FindByChannelWithGatewayConfigTrait;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\PaymentMethodRepository as BasePaymentMethodRepository;

final class PaymentMethodRepository extends BasePaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    use FindByChannelWithGatewayConfigTrait;
}
