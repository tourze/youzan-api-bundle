<?php

namespace YouzanApiBundle\Tests\Unit\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Shop;
use YouzanApiBundle\Repository\ShopRepository;

class ShopRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;
    private ShopRepository $repository;
    
    protected function setUp(): void
    {
        // 创建模拟对象
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 配置ManagerRegistry模拟
        $this->registry->expects($this->any())
            ->method('getManagerForClass')
            ->with(Shop::class)
            ->willReturn($this->entityManager);
        
        // 创建被测仓库
        $this->repository = new ShopRepository($this->registry);
    }
    
    public function testFindByKdtId_withExistingShop(): void
    {
        // 创建模拟商店
        $shop = new Shop();
        $shop->setName('Test Shop')
            ->setKdtId('test_kdt_id');
        
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
            ->with(Shop::class)
            ->willReturnSelf();
        
        $this->entityManager->expects($this->once())
            ->method('findOneBy')
            ->with(['kdtId' => 'test_kdt_id'])
            ->willReturn($shop);
        
        // 执行被测方法
        $result = $this->repository->findByKdtId('test_kdt_id');
        
        // 验证结果
        $this->assertSame($shop, $result);
    }
    
    public function testFindByKdtId_withNonExistingShop(): void
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
            ->with(Shop::class)
            ->willReturnSelf();
        
        $this->entityManager->expects($this->once())
            ->method('findOneBy')
            ->with(['kdtId' => 'non_existing_kdt_id'])
            ->willReturn(null);
        
        // 执行被测方法
        $result = $this->repository->findByKdtId('non_existing_kdt_id');
        
        // 验证结果
        $this->assertNull($result);
    }
} 