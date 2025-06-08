<?php

namespace YouzanApiBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private AccountRepository $repository;
    
    protected function setUp(): void
    {
        // 创建模拟对象
        $this->registry = $this->createMock(ManagerRegistry::class);
        
        // 创建被测仓库
        $this->repository = $this->createPartialMock(
            AccountRepository::class,
            ['findOneBy']
        );
    }
    
    public function testFindByClientId_withExistingAccount(): void
    {
        // 创建模拟账号
        $account = new Account();
        $account->setName('Test Account')
            ->setClientId('test_client_id')
            ->setClientSecret('test_client_secret');
        
        // 配置模拟方法
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['clientId' => 'test_client_id'])
            ->willReturn($account);
        
        // 执行被测方法
        $result = $this->repository->findByClientId('test_client_id');
        
        // 验证结果
        $this->assertSame($account, $result);
    }
    
    public function testFindByClientId_withNonExistingAccount(): void
    {
        // 配置模拟方法
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['clientId' => 'non_existing_client_id'])
            ->willReturn(null);
        
        // 执行被测方法
        $result = $this->repository->findByClientId('non_existing_client_id');
        
        // 验证结果
        $this->assertNull($result);
    }
} 