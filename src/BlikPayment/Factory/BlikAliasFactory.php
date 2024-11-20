<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\BlikPayment\Factory;

use CommerceWeavers\SyliusTpayPlugin\BlikPayment\Entity\BlikAliasInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

final class BlikAliasFactory implements BlikAliasFactoryInterface
{
    /**
     * @param class-string<BlikAliasInterface> $className
     */
    public function __construct(private readonly string $className)
    {
        if (!is_a($className, BlikAliasInterface::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as resource',
                BlikAliasInterface::class,
            ));
        }
    }

    public function createNew(): BlikAliasInterface
    {
        return new $this->className();
    }

    public function createForCustomerAndChannel(
        CustomerInterface $customer,
        ChannelInterface $channel,
    ): BlikAliasInterface {
        $blikAlias = $this->createNew();
        $blikAlias->setCustomer($customer);
        $blikAlias->setChannel($channel);

        return $blikAlias;
    }
}
