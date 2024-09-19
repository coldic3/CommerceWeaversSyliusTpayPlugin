<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddShopPayOperationToAllowedNonGetOperationsPass implements CompilerPassInterface
{
    public const SHOP_PAY_ACTION_NAME = 'shop_pay';

    public function process(ContainerBuilder $container): void
    {
        $container->setParameter(
            'sylius.api.doctrine_extension.order_visitor_item.filter_cart.allowed_non_get_operations',
            array_merge(
                $container->getParameter('sylius.api.doctrine_extension.order_visitor_item.filter_cart.allowed_non_get_operations'),
                [self::SHOP_PAY_ACTION_NAME],
            ),
        );
        $container->setParameter(
            'sylius.api.doctrine_extension.order_shop_user_item.filter_cart.allowed_non_get_operations',
            array_merge(
                $container->getParameter('sylius.api.doctrine_extension.order_shop_user_item.filter_cart.allowed_non_get_operations'),
                ['self::SHOP_PAY_ACTION_NAME'],
            ),
        );
    }
}
