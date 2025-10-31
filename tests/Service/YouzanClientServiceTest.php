<?php

namespace YouzanApiBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Exception\AccessTokenException;
use YouzanApiBundle\Service\YouzanClientService;

/**
 * 测试YouzanClientService服务
 *
 * @internal
 */
#[CoversClass(YouzanClientService::class)]
#[RunTestsInSeparateProcesses]
final class YouzanClientServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 不需要特殊的设置逻辑
    }

    private function getYouzanClientService(): YouzanClientService
    {
        return self::getService(YouzanClientService::class);
    }

    public function testGetClient(): void
    {
        // 准备测试数据
        $account = new Account();
        $account->setName('Test Account');
        $account->setClientId('test_client_id');
        $account->setClientSecret('test_client_secret');

        // 由于getClient方法需要调用真实的有赞API，在测试环境中会失败
        // 这里测试异常情况，验证当API调用失败时的错误处理
        $this->expectException(AccessTokenException::class);
        $this->expectExceptionMessage('Failed to get access token from Youzan API');

        // 执行被测方法，预期会抛出异常
        $this->getYouzanClientService()->getClient($account);
    }

    public function testGetClientByClientIdWithExistingAccount(): void
    {
        $service = $this->getYouzanClientService();

        // 先创建一个账号
        $account = $service->createAccount('Test Account', 'test_client_id', 'test_client_secret');

        // 由于getClient方法需要调用真实的有赞API，在测试环境中会失败
        // 这里测试异常情况，验证当API调用失败时的错误处理
        $this->expectException(AccessTokenException::class);
        $this->expectExceptionMessage('Failed to get access token from Youzan API');

        // 执行被测方法
        $service->getClientByClientId('test_client_id');
    }

    public function testGetClientByClientIdWithNonExistingAccount(): void
    {
        // 执行被测方法，使用不存在的客户端ID
        $client = $this->getYouzanClientService()->getClientByClientId('non_existing_client_id');

        // 验证结果
        $this->assertNull($client);
    }

    public function testCreateAccount(): void
    {
        // 执行被测方法
        $account = $this->getYouzanClientService()->createAccount('New Account', 'new_client_id', 'new_client_secret');

        // 验证结果
        $this->assertSame('New Account', $account->getName());
        $this->assertSame('new_client_id', $account->getClientId());
        $this->assertSame('new_client_secret', $account->getClientSecret());
        $this->assertNotNull($account->getCreateTime());
        $this->assertNotNull($account->getUpdateTime());

        // 验证账号已经被持久化到数据库
        $this->assertNotNull($account->getId());
    }
}
