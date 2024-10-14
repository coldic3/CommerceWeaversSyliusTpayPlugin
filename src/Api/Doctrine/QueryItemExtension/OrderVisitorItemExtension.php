<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Api\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface as LegacyQueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderVisitorItemExtension implements QueryItemExtensionInterface
{
    public const SHOP_PAY_OPERATION = 'shop_pay';

    public const SHOP_CANCEL_LAST_PAYMENT_OPERATION = 'shop_cancel_last_payment';

    public function __construct(
        private readonly QueryItemExtensionInterface $decorated,
        private readonly UserContextInterface $userContext,
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
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        if (!in_array($operationName, $this->getAllowedOperations(), true)) {
            $this->decorated->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);

            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->leftJoin(sprintf('%s.customer', $rootAlias), 'customer')
            ->leftJoin('customer.user', 'user')
            ->andWhere($queryBuilder->expr()->orX(
                'user IS NULL',
                sprintf('%s.customer IS NULL', $rootAlias),
                $queryBuilder->expr()->andX(
                    sprintf('%s.customer IS NOT NULL', $rootAlias),
                    sprintf('%s.createdByGuest = :createdByGuest', $rootAlias),
                ),
            ))->setParameter('createdByGuest', true)
        ;
    }

    /**
     * @return array<string>
     */
    private function getAllowedOperations(): array
    {
        return [self::SHOP_PAY_OPERATION, self::SHOP_CANCEL_LAST_PAYMENT_OPERATION];
    }
}
