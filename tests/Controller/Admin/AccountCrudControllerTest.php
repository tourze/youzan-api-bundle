<?php

namespace YouzanApiBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Controller\Admin\AccountCrudController;
use YouzanApiBundle\Entity\Account;

/**
 * 测试AccountCrudController控制器
 */
class AccountCrudControllerTest extends TestCase
{
    private AccountCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new AccountCrudController();
    }

    public function testExtendsAbstractCrudController(): void
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testGetEntityFqcn(): void
    {
        $this->assertSame(Account::class, AccountCrudController::getEntityFqcn());
    }

    public function testConfigureCrud(): void
    {
        /** @var Crud&MockObject $crud */
        $crud = $this->createMock(Crud::class);
        
        // 配置返回自身以支持链式调用
        $crud->method('setEntityLabelInSingular')->willReturnSelf();
        $crud->method('setEntityLabelInPlural')->willReturnSelf();
        $crud->method('setPageTitle')->willReturnSelf();
        $crud->method('setHelp')->willReturnSelf();
        $crud->method('setDefaultSort')->willReturnSelf();
        $crud->method('setSearchFields')->willReturnSelf();

        $result = $this->controller->configureCrud($crud);
        $this->assertSame($crud, $result);
    }

    public function testConfigureFields(): void
    {
        $fields = iterator_to_array($this->controller->configureFields('index'));
        
        // 验证返回的是可迭代对象
        $this->assertNotEmpty($fields);
    }

} 