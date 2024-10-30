<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

use CommerceWeavers\SyliusTpayPlugin\Model\GatewayConfigInterface;
use CommerceWeavers\SyliusTpayPlugin\Payum\Exception\UnableToGetBankListException;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PayGroup;
use CommerceWeavers\SyliusTpayPlugin\Tpay\PaymentType;
use Payum\Core\Security\CypherInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ValidTpayChannelListProvider implements ValidTpayChannelListProviderInterface
{
    public function __construct(
        private readonly AvailableTpayChannelListProviderInterface $availableTpayApiBankListProvider,
        private readonly RepositoryInterface $gatewayRepository,
        private readonly CypherInterface $cypher,
    ) {
    }

    public function provide(): array
    {
        $availableChannels = $this->availableTpayApiBankListProvider->provide();

        /** @var GatewayConfigInterface[] $tpayGatewayConfigs */
        $tpayGatewayConfigs = $this->gatewayRepository->findBy(['gatewayName' => 'tpay']);

        Assert::notEmpty($tpayGatewayConfigs, 'There is no gateway config of Tpay type available');

        if (count($tpayGatewayConfigs) === 1 &&
            $tpayGatewayConfigs[0]->getConfig()['type'] === PaymentType::PAY_BY_LINK
        ) {
            return $availableChannels;
        }

        $paymentMethodsToRemoveByGroupId = [];
        $hasPblPaymentAvailable = false;
        foreach ($tpayGatewayConfigs as $tpayGatewayConfig) {
            // cached doctrine values are encrypted hence need for decrypt
            $tpayGatewayConfig->decrypt($this->cypher);
            $config = $tpayGatewayConfig->getConfig();

            if (!array_key_exists('type', $config)) {
                continue;
            }

            if ($hasPblPaymentAvailable === false && $config['type'] === PaymentType::PAY_BY_LINK) {
                $hasPblPaymentAvailable = true;
            }

            match ($config['type']) {
                PaymentType::VISA_MOBILE => $paymentMethodsToRemoveByGroupId[] = PayGroup::VISA_MOBILE,
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
