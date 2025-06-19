<?php

namespace YouzanApiBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use YouzanApiBundle\YouzanApiBundle;

/**
 * 测试YouzanApiBundle主类
 */
class YouzanApiBundleTest extends TestCase
{
    private YouzanApiBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new YouzanApiBundle();
    }

    public function testExtendsSymfonyBundle(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testBundleCanBeInstantiated(): void
    {
        $bundle = new YouzanApiBundle();
        $this->assertInstanceOf(YouzanApiBundle::class, $bundle);
    }

    public function testBundleNameIsCorrect(): void
    {
        $this->assertSame('YouzanApiBundle', $this->bundle->getName());
    }

    public function testBundlePathExists(): void
    {
        $path = $this->bundle->getPath();
        $this->assertNotEmpty($path);
    }
} 