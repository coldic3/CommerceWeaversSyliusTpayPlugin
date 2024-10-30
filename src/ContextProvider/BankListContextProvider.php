<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\ContextProvider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Component\Core\Model\OrderInterface;

final class BankListContextProvider implements ContextProviderInterface
{
    public function __construct(
        private readonly ValidTpayChannelListProviderInterface $validatedTpayApiBankListProvider,
    ) {
    }

    public function provide(array $templateContext, TemplateBlock $templateBlock): array
    {
        // TODO this is runned few many times when on checkout/complete/choice-item
        // propably there it should not be runned totaly but on summary step only
        if (isset($templateContext['order'])) {
            /** @var OrderInterface $order */
            $order = $templateContext['order'];

            if (null === $order->getLastPayment()) {
                return $templateContext;
            }
        }

        $templateContext['banks'] = $this->validatedTpayApiBankListProvider->provide();

        return $templateContext;
    }

    public function supports(TemplateBlock $templateBlock): bool
    {
        return ('sylius.shop.checkout.complete.summary' === $templateBlock->getEventName() &&
            'pay_by_link' === $templateBlock->getName()) ||
            ('cw.tpay.shop.select_payment.choice_item_form' === $templateBlock->getEventName() &&
            'pay_by_link' === $templateBlock->getName())
        ;
    }
}
