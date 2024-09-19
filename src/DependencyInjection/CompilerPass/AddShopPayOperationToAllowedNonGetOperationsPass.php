<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AddShopPayOperationToAllowedNonGetOperationsPass implements CompilerPassInterface
{
    public const SHOP_PAY_ACTION_NAME = 'shop_pay';

    public const SYLIUS_API_DOCTRINE_EXTENSION_ORDER_VISITOR_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS = 'sylius.api.doctrine_extension.order_visitor_item.filter_cart.allowed_non_get_operations';

    public const SYLIUS_API_DOCTRINE_EXTENSION_ORDER_SHOP_USER_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS = 'sylius.api.doctrine_extension.order_shop_user_item.filter_cart.allowed_non_get_operations';

    public function process(ContainerBuilder $container): void
    {
        if ($container->hasParameter(self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_VISITOR_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS)) {
            /** @var array<string> $orderVisitorItemAllowedNonGetOperations */
            $orderVisitorItemAllowedNonGetOperations = $container->getParameter(self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_VISITOR_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS);
            $container->setParameter(
                self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_VISITOR_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS,
                array_merge(
                    $orderVisitorItemAllowedNonGetOperations,
                    [self::SHOP_PAY_ACTION_NAME],
                ),
            );
        }

        if ($container->hasParameter(self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_SHOP_USER_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS)) {
            /** @var array<string> $orderShopUserItemAllowedNonGetOperations */
            $orderShopUserItemAllowedNonGetOperations = $container->getParameter(self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_SHOP_USER_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS);
            $container->setParameter(
                self::SYLIUS_API_DOCTRINE_EXTENSION_ORDER_SHOP_USER_ITEM_FILTER_CART_ALLOWED_NON_GET_OPERATIONS,
                array_merge(
                    $orderShopUserItemAllowedNonGetOperations,
                    ['self::SHOP_PAY_ACTION_NAME'],
                ),
            );
        }
    }
}
