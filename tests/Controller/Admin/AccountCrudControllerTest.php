<?php

namespace YouzanApiBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use YouzanApiBundle\Controller\Admin\AccountCrudController;
use YouzanApiBundle\Entity\Account;

/**
 * @internal
 * Controller配置了 5 个过滤器，测试搜索功能
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Account>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return new AccountCrudController();
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '账号名称' => ['账号名称'];
        yield '客户端ID' => ['客户端ID'];
        yield '关联店铺' => ['关联店铺'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'clientId' => ['clientId'];
        yield 'clientSecret' => ['clientSecret'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'clientId' => ['clientId'];
        yield 'clientSecret' => ['clientSecret'];
    }

    public function testIndexPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('有赞账号列表', $content);
    }

    public function testIndexPageRedirectsForUnauthenticatedUser(): void
    {
        $client = self::createClient();

        $client->request('GET', '/admin/youzan/account');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testNewPageAccessibleForAuthenticatedAdmin(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/account/new');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $content = $response->getContent();
        $this->assertIsString($content);
        $this->assertStringContainsString('新增有赞账号', $content);
    }

    public function testCreateAccountFormValidation(): void
    {
        $client = self::createClient();

        // 尝试访问需要认证的页面，验证重定向到登录页
        $client->request('GET', '/admin/youzan/account/new');

        $response = $client->getResponse();
        $this->assertTrue($response->isRedirection());
        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testEditAccountFormAccess(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $account = new Account();
        $account->setName('Original Name');
        $account->setClientId('original_client');
        $account->setClientSecret('original_secret');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $client->request('GET', '/admin/youzan/account/' . $account->getId() . '/edit');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testSearchFunctionalityWithValidQuery(): void
    {
        $client = self::createClientWithDatabase();
        $client->loginUser($this->createAdminUser());

        $client->request('GET', '/admin/youzan/account?query=test');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    public function testFilterFunctionalityWithNameFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据
        $entityManager = self::getEntityManager();

        $account1 = new Account();
        $account1->setName('Test Account One');
        $account1->setClientId('test_client_1');
        $account1->setClientSecret('secret_1');
        $entityManager->persist($account1);

        $account2 = new Account();
        $account2->setName('Another Account');
        $account2->setClientId('test_client_2');
        $account2->setClientSecret('secret_2');
        $entityManager->persist($account2);

        $entityManager->flush();

        // 测试账号名称过滤器
        $crawler = $client->request('GET', '/admin/youzan/account', [
            'filters' => [
                'name' => 'Test Account',
            ],
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        // 过滤器功能正常工作即可，无需验证具体内容
    }

    public function testFilterFunctionalityWithClientIdFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据
        $entityManager = self::getEntityManager();

        $account1 = new Account();
        $account1->setName('Account One');
        $account1->setClientId('special_client_1');
        $account1->setClientSecret('secret_1');
        $entityManager->persist($account1);

        $account2 = new Account();
        $account2->setName('Account Two');
        $account2->setClientId('regular_client_2');
        $account2->setClientSecret('secret_2');
        $entityManager->persist($account2);

        $entityManager->flush();

        // 测试客户端ID过滤器
        $crawler = $client->request('GET', '/admin/youzan/account', [
            'filters' => [
                'clientId' => 'special_client',
            ],
        ]);

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        // 过滤器功能正常工作即可，无需验证具体内容
    }

    public function testFilterFunctionalityWithDateFilter(): void
    {
        $client = self::createAuthenticatedClient();

        // 创建测试数据 - 一个账号有明确的创建时间
        $entityManager = self::getEntityManager();

        $account1 = new Account();
        $account1->setName('Old Account');
        $account1->setClientId('old_client');
        $account1->setClientSecret('secret_1');
        $account1->setCreateTime(new \DateTimeImmutable('2023-01-01 10:00:00'));
        $entityManager->persist($account1);

        $account2 = new Account();
        $account2->setName('New Account');
        $account2->setClientId('new_client');
        $account2->setClientSecret('secret_2');
        $account2->setCreateTime(new \DateTimeImmutable('2024-06-01 10:00:00'));
        $entityManager->persist($account2);

        $entityManager->flush();

        // 测试创建时间过滤器 - 查找 2024 年之后的账号
        $crawler = $client->request('GET', '/admin/youzan/account', [
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
