<?php

namespace YouzanApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Extension\Extension;
use YouzanApiBundle\DependencyInjection\YouzanApiExtension;

/**
 * 测试YouzanApiExtension
 */
class YouzanApiExtensionTest extends TestCase
{
    private YouzanApiExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new YouzanApiExtension();
    }

    public function testExtendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function testLoadMethodExists(): void
    {
        // 测试load方法是否存在
        $this->assertTrue(method_exists($this->extension, 'load'));
    }

    public function testExtensionImplementsLoadMethod(): void
    {
        // 验证扩展有load方法的签名
        $reflection = new \ReflectionClass($this->extension);
        $method = $reflection->getMethod('load');
        
        $this->assertSame('load', $method->getName());
        $this->assertSame(2, $method->getNumberOfParameters());
    }
} 