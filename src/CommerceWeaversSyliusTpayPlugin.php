<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

final class CommerceWeaversSyliusTpayPlugin extends AbstractBundle
{
    use SyliusPluginTrait;
}
