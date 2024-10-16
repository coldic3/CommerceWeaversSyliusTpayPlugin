<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Tpay\Provider;

interface TpayApiBankListProviderInterface
{
    /** @return array{
     *     id: string,
     *     name: string,
     *     fullName: string,
     *     image: object,
     *     available: bool,
     *     onlinePayment: bool,
     *     instantRedirection: bool,
     *     groups: array,
     *     constraints: array
     * }
     */
    public function provide(): array;
}
