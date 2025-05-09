<?php

namespace YouzanApiBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Tourze\DoctrineIndexedBundle\DoctrineIndexedBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\SnowflakeBundle\SnowflakeBundle;
use YouzanApiBundle\YouzanApiBundle;

class IntegrationTestKernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new DoctrineBundle();
        yield new SnowflakeBundle();
        yield new DoctrineSnowflakeBundle();
        yield new DoctrineIndexedBundle();
        yield new DoctrineTimestampBundle();
        yield new YouzanApiBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        // 基本框架配置
        $container->extension('framework', [
            'secret' => 'TEST_SECRET',
            'test' => true,
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => [
                'log' => true,
            ],
        ]);

        // Doctrine 配置 - 使用内存数据库
        $container->extension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'url' => 'sqlite:///:memory:',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'controller_resolver' => [
                    'auto_mapping' => false,
                ],
                'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
                'auto_mapping' => true,
                'mappings' => [
                    'YouzanApiBundle' => [
                        'is_bundle' => true,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/packages/youzan-api-bundle/src/Entity',
                        'prefix' => 'YouzanApiBundle\Entity',
                    ],
                    'TestEntities' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/packages/youzan-api-bundle/tests/Integration/Entity',
                        'prefix' => 'YouzanApiBundle\Tests\Integration\Entity',
                    ],
                ],
            ],
        ]);
    }

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/packages/youzan-api-bundle/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/packages/youzan-api-bundle/var/log';
    }
} 