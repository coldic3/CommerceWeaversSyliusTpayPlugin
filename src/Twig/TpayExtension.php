<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class TpayExtension extends AbstractExtension
{
    /**
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('cw_tpay_get_gateway_config_value', [TpayRuntime::class, 'getConfigValue']),
        ];
    }

    /**
     * @return array<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('cw_tpay_convert_minor_to_major_currency', [TpayRuntime::class, 'convertMinorToMajorCurrency']),
        ];
    }
}
