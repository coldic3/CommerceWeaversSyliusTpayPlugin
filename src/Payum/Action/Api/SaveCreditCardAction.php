<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Payum\Action\Api;

use CommerceWeavers\SyliusTpayPlugin\Entity\CreditCardInterface;
use CommerceWeavers\SyliusTpayPlugin\Model\PaymentDetails;
use CommerceWeavers\SyliusTpayPlugin\Payum\Request\Api\SaveCreditCard;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Generic;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class SaveCreditCardAction extends BasePaymentAwareAction implements GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function __construct(
        private readonly FactoryInterface $creditCardFactory,
        private readonly RepositoryInterface $creditCardRepository,
    ) {
        parent::__construct();
    }

    /**
     * @param SaveCreditCard $request
     */
    protected function doExecute(Generic $request, PaymentInterface $model, PaymentDetails $paymentDetails, string $gatewayName, string $localeCode): void
    {
        /** @var CreditCardInterface $creditCard */
        $creditCard = $this->creditCardFactory->createNew();

        $creditCard->setToken($request->cardToken);
        $creditCard->setBrand($request->cardBrand);
        $creditCard->setTail($request->cardTail);
        $creditCard->setCustomer($model->getOrder()->getCustomer());
        $creditCard->setExpirationDate(new \DateTimeImmutable(
            sprintf(
                '01-%s-20%s',
                substr($request->tokenExpiryDate, 0, 2),
                substr($request->tokenExpiryDate, 2, 2)
            )
        ));

        $this->creditCardRepository->add($creditCard);
    }

    public function supports($request): bool
    {
        return $request instanceof SaveCreditCard && $request->getModel() instanceof PaymentInterface;
    }
}
