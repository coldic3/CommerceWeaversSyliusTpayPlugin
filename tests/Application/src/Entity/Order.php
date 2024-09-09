<?php

declare(strict_types=1);

namespace App\Entity;

use CommerceWeavers\SyliusTpayPlugin\Entity\OrderLastNewPaymentAwareInterface;
use CommerceWeavers\SyliusTpayPlugin\Entity\OrderLastNewPaymentAwareTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Order as BaseOrder;

#[ORM\Entity]
#[ORM\Table(name: 'sylius_order')]
class Order extends BaseOrder implements OrderLastNewPaymentAwareInterface
{
    use OrderLastNewPaymentAwareTrait;
}
