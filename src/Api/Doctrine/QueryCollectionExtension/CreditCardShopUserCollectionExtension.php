<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;

final class CreditCardShopUserCollectionExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        LegacyQueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, CreditCardInterface::class, true)) {
            return;
        }

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
