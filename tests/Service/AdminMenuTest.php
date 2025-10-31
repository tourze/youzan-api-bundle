<?php

namespace YouzanApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use YouzanApiBundle\Service\AdminMenu;

/**
 * 测试AdminMenu服务
 *
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testServiceCreation(): void
    {
        // 从容器获取服务
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testServiceIsCallable(): void
    {
        // 从容器获取服务并测试其基本功能
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertIsCallable($adminMenu, 'AdminMenu service should be callable');
    }
}
