<?php

namespace YouzanApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use YouzanApiBundle\Exception\AccessTokenException;
use YouzanApiBundle\Exception\YouzanApiException;

/**
 * 测试AccessTokenException异常类
 *
 * @internal
 */
#[CoversClass(AccessTokenException::class)]
final class AccessTokenExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new AccessTokenException('Test message');

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertInstanceOf(YouzanApiException::class, $exception);
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithCode(): void
    {
        $exception = new AccessTokenException('Test message', 500);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new AccessTokenException('Test message', 0, $previous);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
