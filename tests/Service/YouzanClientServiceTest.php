<?php

namespace YouzanApiBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;
use YouzanApiBundle\Service\YouzanClientService;

/**
 * 测试YouzanClientService服务
 */
class YouzanClientServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private AccountRepository $accountRepository;
    private YouzanClientService $service;
    
    protected function setUp(): void
    {
        // 创建模拟对象
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        
        // 创建被测服务
        $this->service = new YouzanClientService($this->entityManager, $this->accountRepository);
    }
    
    public function testGetClient(): void
    {
        // 准备测试数据
        $account = new Account();
        $account->setName('Test Account')
            ->setClientId('test_client_id')
            ->setClientSecret('test_client_secret');
        
        // 执行被测方法
        $client = $this->service->getClient($account);
        
        // 由于我们使用了模拟类，只能验证返回了非null的结果
        $this->assertNotNull($client);
        
        // 再次调用应该使用缓存
        $cachedClient = $this->service->getClient($account);
        $this->assertSame($client, $cachedClient);
    }
    
    public function testGetClientByClientId_withExistingAccount(): void
    {
        // 准备测试数据
        $account = new Account();
        $account->setName('Test Account')
            ->setClientId('test_client_id')
            ->setClientSecret('test_client_secret');
        
        // 配置模拟对象
        $this->accountRepository->expects($this->once())
            ->method('findByClientId')
            ->with('test_client_id')
            ->willReturn($account);
        
        // 执行被测方法
        $client = $this->service->getClientByClientId('test_client_id');
        
        // 验证结果
        $this->assertNotNull($client);
    }
    
    public function testGetClientByClientId_withNonExistingAccount(): void
    {
        // 配置模拟对象
        $this->accountRepository->expects($this->once())
            ->method('findByClientId')
            ->with('non_existing_client_id')
            ->willReturn(null);
        
        // 执行被测方法
        $client = $this->service->getClientByClientId('non_existing_client_id');
        
        // 验证结果
        $this->assertNull($client);
    }
    
    public function testCreateAccount(): void
    {
        // 配置模拟对象
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($account) {
                return $account instanceof Account
                    && $account->getName() === 'New Account'
                    && $account->getClientId() === 'new_client_id'
                    && $account->getClientSecret() === 'new_client_secret';
            }));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        // 执行被测方法
        $account = $this->service->createAccount('New Account', 'new_client_id', 'new_client_secret');
        
        // 验证结果
        $this->assertInstanceOf(Account::class, $account);
        $this->assertSame('New Account', $account->getName());
        $this->assertSame('new_client_id', $account->getClientId());
        $this->assertSame('new_client_secret', $account->getClientSecret());
    }
} 