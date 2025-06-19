<?php

namespace YouzanApiBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use YouzanApiBundle\Service\AdminMenu;

/**
 * 测试AdminMenu服务
 */
class AdminMenuTest extends TestCase
{
    private LinkGeneratorInterface&MockObject $linkGenerator;
    private AdminMenu $adminMenu;

    protected function setUp(): void
    {
        // 创建模拟对象
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        
        // 创建被测服务
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }

    public function testConstructorInjectsLinkGenerator(): void
    {
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }


    public function testInvokeWithMockItem(): void
    {
        // 创建简单的模拟对象
        $rootItem = $this->createMock(ItemInterface::class);
        $youzanItem = $this->createMock(ItemInterface::class);
        $subItem = $this->createMock(ItemInterface::class);
        
        // 配置根菜单项的行为
        $rootItem->method('getChild')
            ->with('有赞API管理')
            ->willReturnOnConsecutiveCalls(null, $youzanItem);
        
        $rootItem->method('addChild')
            ->with('有赞API管理')
            ->willReturn($youzanItem);
        
        // 配置有赞菜单项的行为
        $youzanItem->method('addChild')->willReturn($subItem);
        $subItem->method('setUri')->willReturnSelf();
        $subItem->method('setAttribute')->willReturnSelf();
        
        // 配置链接生成器返回虚拟URL
        $this->linkGenerator->method('getCurdListPage')->willReturn('/admin/test');
        
        // 执行被测方法 - 主要验证不抛出异常
        ($this->adminMenu)($rootItem);
        
        // 如果到这里没抛异常，说明基本功能正常
        $this->assertTrue(true);
    }

    public function testServiceCanBeInstantiated(): void
    {
        $service = new AdminMenu($this->linkGenerator);
        $this->assertInstanceOf(AdminMenu::class, $service);
    }
} 