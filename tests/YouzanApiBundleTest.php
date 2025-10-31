<?php

declare(strict_types=1);

namespace YouzanApiBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use YouzanApiBundle\YouzanApiBundle;

/**
 * @internal
 */
#[CoversClass(YouzanApiBundle::class)]
#[RunTestsInSeparateProcesses]
final class YouzanApiBundleTest extends AbstractBundleTestCase
{
}
