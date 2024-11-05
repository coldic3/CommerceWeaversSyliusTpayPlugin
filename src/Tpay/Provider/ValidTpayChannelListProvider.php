<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

use App\Repository\PaymentMethodRepositoryInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\UnableToGetBankListException;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PaymentType;
use Payum\Core\Security\CryptedInterface;
use Payum\Core\Security\CypherInterface;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class ValidTpayChannelListProvider implements ValidTpayChannelListProviderInterface
{
    public function __construct(
        private readonly AvailableTpayChannelListProviderInterface $availableTpayApiBankListProvider,
        private readonly PaymentMethodRepositoryInterface $paymentMethodRepository,
        private readonly ChannelContextInterface $channelContext,
        private readonly CypherInterface $cypher,
    ) {
    }

    public function provide(): array
    {
        $availableChannels = $this->availableTpayApiBankListProvider->provide();

        /** @var PaymentMethodInterface[] $paymentMethods */
        $paymentMethods = $this->paymentMethodRepository->findByChannelAndGatewayConfigNameWithGatewayConfig(
            $this->channelContext->getChannel(),
            'tpay',
        );

        Assert::notEmpty($paymentMethods, 'There is no payment method of Tpay type available');

        $paymentMethodsToRemoveByGroupId = [];
        $hasPblPaymentAvailable = false;
        foreach ($paymentMethods as $paymentMethod) {
            /** @var (GatewayConfigInterface&CryptedInterface)|null $tpayGatewayConfig */
            $tpayGatewayConfig = $paymentMethod->getGatewayConfig();

            if (null === $tpayGatewayConfig) {
                continue;
            }

            $tpayGatewayConfig->decrypt($this->cypher);
            $config = $tpayGatewayConfig->getConfig();

            if (!array_key_exists('type', $config)) {
                continue;
            }

            if ($hasPblPaymentAvailable === false && $config['type'] === PaymentType::PAY_BY_LINK) {
                $hasPblPaymentAvailable = true;
            }

            match ($config['type']) {
                PaymentType::VISA_MOBILE => array_push(
                    $paymentMethodsToRemoveByGroupId,
                    PayGroup::VISA_MOBILE,
                    PayGroup::VISA_MOBILE_ON_SITE,
                ),
                PaymentType::APPLE_PAY => $paymentMethodsToRemoveByGroupId[] = PayGroup::APPLE_PAY,
                PaymentType::GOOGLE_PAY => $paymentMethodsToRemoveByGroupId[] = PayGroup::GOOGLE_PAY,
                PaymentType::BLIK => $paymentMethodsToRemoveByGroupId[] = PayGroup::BLIK,
                PaymentType::CARD => $paymentMethodsToRemoveByGroupId[] = PayGroup::CARD,
                default => null,
            };
        }

        if (!$hasPblPaymentAvailable) {
            throw new UnableToGetBankListException(
                'Bank list cannot be retrieved if there is no payment method with PayByLink type configured',
            );
        }

        return array_filter($availableChannels, static function (array $channel) use ($paymentMethodsToRemoveByGroupId): bool {
            $groupId = (int) $channel['groups'][0]['id'];

            return !in_array($groupId, $paymentMethodsToRemoveByGroupId, true);
        });
    }
}
