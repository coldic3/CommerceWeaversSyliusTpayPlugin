<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\ContextProvider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Component\Core\Model\OrderInterface;

final class BankListContextProvider implements ContextProviderInterface
{
    public function __construct(
        private readonly ValidTpayChannelListProviderInterface $validTpayChannelListProvider,
    ) {
    }

    public function provide(array $templateContext, TemplateBlock $templateBlock): array
    {
        if (isset($templateContext['order'])) {
            /** @var OrderInterface $order */
            $order = $templateContext['order'];

            if (null === $order->getLastPayment()) {
                return $templateContext;
            }
        }

        $templateContext['banks'] = $this->validTpayChannelListProvider->provide();

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
