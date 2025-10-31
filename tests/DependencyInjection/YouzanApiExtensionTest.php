<?php

namespace YouzanApiBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use YouzanApiBundle\DependencyInjection\YouzanApiExtension;

/**
 * 测试YouzanApiExtension
 *
 * @internal
 */
#[CoversClass(YouzanApiExtension::class)]
final class YouzanApiExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private YouzanApiExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new YouzanApiExtension();
    }

    public function testExtendsSymfonyExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
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
