<?php

namespace YouzanApiBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use YouzanApiBundle\Exception\AccessTokenException;
use YouzanApiBundle\Exception\YouzanApiException;

/**
 * 测试YouzanApiException异常类
 *
 * @internal
 */
#[CoversClass(YouzanApiException::class)]
final class YouzanApiExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionInheritance(): void
    {
        $exception = new AccessTokenException('Test message');

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertInstanceOf(YouzanApiException::class, $exception);
    }

    public function testExceptionWithCode(): void
    {
        $exception = new AccessTokenException('Test message', 404);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame(404, $exception->getCode());
    }

    public function testExceptionWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new AccessTokenException('Test message', 0, $previous);

        $this->assertSame('Test message', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}
