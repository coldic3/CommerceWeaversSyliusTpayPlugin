<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\CardPayment\Factory;

use CommerceWeavers\SyliusTpayPlugin\CardPayment\Entity\CreditCardInterface;
use Symfony\Component\Uid\Uuid;

final class CreditCardFactory implements CreditCardFactoryInterface
{
    /**
     * @param class-string<CreditCardInterface> $className
     */
    public function __construct(private readonly string $className)
    {
        if (!is_a($className, CreditCardInterface::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as resource',
                CreditCardInterface::class,
            ));
        }
    }

    public function createNew(): CreditCardInterface
    {
        return new $this->className(Uuid::v4()->toRfc4122());
    }
}
