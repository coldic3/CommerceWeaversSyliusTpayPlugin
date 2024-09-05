<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class CommerceWeaversSyliusTpayPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function getPath(): string
    {
        if (!isset($this->path)) {
            $reflected = new \ReflectionObject($this);
            $this->path = \dirname($reflected->getFileName(), 2);
        }

        return $this->path;
    }
}
