<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\PayByLinkPayment\ContextProvider;

use CommerceWeavers\SyliusTpayPlugin\Tpay\Provider\ValidTpayChannelListProviderInterface;
use Sylius\Bundle\UiBundle\ContextProvider\ContextProviderInterface;
use Sylius\Bundle\UiBundle\Registry\TemplateBlock;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class BankListContextProvider implements ContextProviderInterface
{
    private const SUPPORTED_TEMPLATE_BLOCK_EVENT_NAMES = [
        'sylius.shop.checkout.complete.summary',
        'cw.tpay.shop.select_payment.choice_item_form',
    ];

    public function __construct(
        private readonly ValidTpayChannelListProviderInterface $validTpayChannelListProvider,
    ) {
    }

    public function provide(array $templateContext, TemplateBlock $templateBlock): array
    {
        $templateContext['defaultTpayChannelId'] = null;
        $templateContext['banks'] = [];

        $paymentMethod = $this->resolvePaymentMethod($templateContext);
        $gatewayConfig = $paymentMethod?->getGatewayConfig()?->getConfig() ?? [];

        if (
            null === $paymentMethod ||
            'pay_by_link' !== ($gatewayConfig['type'] ?? null)
        ) {
            return $templateContext;
        }

        /**
         * @phpstan-ignore-next-line
         *
         * @var string|null $tpayChannelId
         */
        $tpayChannelId = $gatewayConfig['tpay_channel_id'] ?? null;

        $templateContext['defaultTpayChannelId'] = $tpayChannelId;
        $templateContext['banks'] = null === $tpayChannelId ? $this->validTpayChannelListProvider->provide() : [];

        return $templateContext;
    }

    public function supports(TemplateBlock $templateBlock): bool
    {
        return
            'pay_by_link' === $templateBlock->getName() &&
            in_array($templateBlock->getEventName(), self::SUPPORTED_TEMPLATE_BLOCK_EVENT_NAMES, true)
        ;
    }

    private function resolvePaymentMethod(array $templateContext): ?PaymentMethodInterface
    {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $templateContext['method'] ?? null;
        if (null !== $paymentMethod) {
            return $paymentMethod;
        }

        /** @var OrderInterface|null $order */
        $order = $templateContext['order'] ?? null;
        if (null === $order) {
            return null;
        }

        $payment = $order->getLastPayment();
        if (null === $payment) {
            return null;
        }

        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $payment->getMethod();

        return $paymentMethod;
    }
}
