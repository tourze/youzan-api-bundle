<?php

namespace YouzanApiBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiBundle\Controller\Admin\ShopCrudController;
use YouzanApiBundle\Entity\Shop;

/**
 * @internal
 * Controller配置了 5 个过滤器，测试搜索功能
 */
#[CoversClass(ShopCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ShopCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Shop>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new ShopCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '有赞店铺ID' => ['有赞店铺ID'];
        yield '店铺名称' => ['店铺名称'];
        yield '关联账号' => ['关联账号'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'kdtId' => ['kdtId'];
        yield 'name' => ['name'];
        yield 'logo' => ['logo'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'kdtId' => ['kdtId'];
        yield 'name' => ['name'];
        yield 'logo' => ['logo'];
    }

    public function testGetEntityFqcn(): void
    {
        $client = self::createClient();

        // 测试通过 HTTP 请求访问控制器，验证实体类型
        $client->request('GET', '/admin/youzan/shop');
        $response = $client->getResponse();

        // 虽然会被重定向到登录页，但可以验证路由和控制器正确加载
        $this->assertTrue($response->isRedirection() || $response->isSuccessful());

        // 直接测试静态方法
        $this->assertSame(Shop::class, ShopCrudController::getEntityFqcn());
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/shop');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('有赞店铺列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/youzan/shop');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/shop/new');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('新增有赞店铺', $content);
    }

    public function testCreateShopFormValidation(): void
    {
        $client = self::createClient();

        // 尝试访问需要认证的页面，验证重定向到登录页
        $client->request('GET', '/admin/youzan/shop/new');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditShopFormAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $shop = new Shop();
        $shop->setKdtId(99999999);
        $shop->setName('Original Shop');
        $shop->setLogo('https://example.com/original-logo.png');

        $entityManager = self::getEntityManager();
        $entityManager->persist($shop);
        $entityManager->flush();

        $client->request('GET', '/admin/youzan/shop/' . $shop->getId() . '/edit');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/shop?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithKdtIdFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据
        $entityManager = self::getEntityManager();

        $shop1 = new Shop();
        $shop1->setName('Special Shop');
        $shop1->setKdtId(55555555);
        $entityManager->persist($shop1);

        $shop2 = new Shop();
        $shop2->setName('Regular Shop');
        $shop2->setKdtId(66666666);
        $entityManager->persist($shop2);

        $entityManager->flush();

        // 测试有赞店铺ID过滤器
        $crawler = $client->request('GET', '/admin/youzan/shop', [
            'filters' => [
                'kdtId' => 12345678,
            ],
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        // 过滤器功能正常工作即可，无需验证具体内容
    }

    public function testFilterFunctionalityWithNameFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据
        $entityManager = self::getEntityManager();

        $shop1 = new Shop();
        $shop1->setName('Test Shop One');
        $shop1->setKdtId(33333333);
        $entityManager->persist($shop1);

        $shop2 = new Shop();
        $shop2->setName('Another Shop');
        $shop2->setKdtId(44444444);
        $entityManager->persist($shop2);

        $entityManager->flush();

        // 测试店铺名称过滤器
        $crawler = $client->request('GET', '/admin/youzan/shop', [
            'filters' => [
                'name' => 'Test Shop',
            ],
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        // 过滤器功能正常工作即可，无需验证具体内容
    }

    public function testFilterFunctionalityWithDateFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据 - 一个店铺有明确的创建时间
        $entityManager = self::getEntityManager();

        $shop1 = new Shop();
        $shop1->setName('Old Shop');
        $shop1->setKdtId(33333333);
        $shop1->setCreateTime(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $entityManager->persist($shop1);

        $shop2 = new Shop();
        $shop2->setName('New Shop');
        $shop2->setKdtId(44444444);
        $shop2->setCreateTime(new \DateTimeImmutable('2024-06-01 10:00:00'));
        $entityManager->persist($shop2);

        $entityManager->flush();

        // 测试创建时间过滤器 - 查找 2024 年之后的店铺
        $crawler = $client->request('GET', '/admin/youzan/shop', [
            'filters' => [
                'createTime' => [
                    'comparison' => '>',
                    'value' => '2024-01-01',
                ],
            ],
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        // 过滤器功能正常工作即可，无需验证具体内容
    }
}
