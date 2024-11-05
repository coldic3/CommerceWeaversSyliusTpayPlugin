<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin;

use CommerceWeavers\SyliusTpayPlugin\DependencyInjection\CompilerPass\AddSupportedRefundPaymentMethodPass;
use CommerceWeavers\SyliusTpayPlugin\DependencyInjection\CompilerPass\AddWinzouStateMachineConfigurationPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CommerceWeaversSyliusTpayPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new AddSupportedRefundPaymentMethodPass());
        $container->addCompilerPass(new AddWinzouStateMachineConfigurationPass());
    }

    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName(), 2);
        }

        return $this->path;
    }
}
