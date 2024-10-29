<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\VarDumper\VarDumper;

final class CreditCardShopUserItemExtension implements QueryItemExtensionInterface
{
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, CreditCardInterface::class, true)) {
            return;
        }

        /** @var AdminUserInterface|ShopUserInterface|null $user */
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface) {
            return;
        }

        $channel = $this->channelContext->getChannel();

        $customer = $user?->getCustomer();

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $customerParameterName = $queryNameGenerator->generateParameterName('customer');
        $channelParameterName = $queryNameGenerator->generateParameterName('channel');

        $queryBuilder
            ->andWhere(sprintf('%s.customer = :%s', $rootAlias, $customerParameterName))
            ->andWhere(sprintf('%s.channel = :%s', $rootAlias, $channelParameterName))
            ->setParameter($customerParameterName, $customer)
            ->setParameter($channelParameterName, $channel)
        ;
    }
}
