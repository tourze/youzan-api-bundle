<?php

namespace YouzanApiBundle\Tests\Unit\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;

class AccountRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;
    private AccountRepository $repository;
    
    protected function setUp(): void
    {
        // 创建模拟对象
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 配置ManagerRegistry模拟
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(Account::class)
            ->willReturn($this->entityManager);
        
        // 创建被测仓库
        $this->repository = new AccountRepository($this->registry);
    }
    
    public function testFindByClientId_withExistingAccount(): void
    {
        // 创建模拟账号
        $account = new Account();
        $account->setName('Test Account')
            ->setClientId('test_client_id')
            ->setClientSecret('test_client_secret');
        
        // 配置entityManager模拟
        $this->entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn(new class {
                public function select() { return $this; }
                public function from() { return $this; }
                public function where() { return $this; }
                public function setParameter() { return $this; }
                public function getQuery() { return $this; }
                public function getOneOrNullResult() { return null; }
            });
        
        // 使用反射机制设置repositoryが使用的EntityManager
        $reflection = new \ReflectionClass($this->repository);
        $property = $reflection->getProperty('_em');
        $property->setAccessible(true);
        $property->setValue($this->repository, $this->entityManager);
        
        // 模拟findOneBy方法（这是从ServiceEntityRepository继承的方法）
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Account::class)
            ->willReturnSelf();
        
        $this->entityManager->expects($this->once())
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
        // 配置entityManager模拟
        $this->entityManager->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn(new class {
                public function select() { return $this; }
                public function from() { return $this; }
                public function where() { return $this; }
                public function setParameter() { return $this; }
                public function getQuery() { return $this; }
                public function getOneOrNullResult() { return null; }
            });
        
        // 使用反射机制设置repository使用的EntityManager
        $reflection = new \ReflectionClass($this->repository);
        $property = $reflection->getProperty('_em');
        $property->setAccessible(true);
        $property->setValue($this->repository, $this->entityManager);
        
        // 模拟findOneBy方法
        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Account::class)
            ->willReturnSelf();
        
        $this->entityManager->expects($this->once())
            ->method('findOneBy')
            ->with(['clientId' => 'non_existing_client_id'])
            ->willReturn(null);
        
        // 执行被测方法
        $result = $this->repository->findByClientId('non_existing_client_id');
        
        // 验证结果
        $this->assertNull($result);
    }
} 