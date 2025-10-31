<?php

namespace YouzanApiBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class YouzanApiExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
