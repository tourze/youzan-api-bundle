<?php

namespace YouzanApiBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class YouzanApiBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
        ];
    }
}
