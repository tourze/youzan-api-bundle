<?php

namespace YouzanApiBundle\Tests\Repository;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Shop;
use YouzanApiBundle\Repository\ShopRepository;

class ShopRepositoryTest extends TestCase
{
    private ShopRepository $repository;
    
    protected function setUp(): void
    {
        
        // 创建被测仓库
        $this->repository = $this->createPartialMock(
            ShopRepository::class,
            ['findOneBy']
        );
    }
    
    public function testFindByKdtId_withExistingShop(): void
    {
        // 创建模拟商店
        $shop = new Shop();
        $shop->setName('Test Shop')
            ->setKdtId(12345);
        
        // 配置模拟方法
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['kdtId' => 12345])
            ->willReturn($shop);
        
        // 执行被测方法
        $result = $this->repository->findByKdtId(12345);
        
        // 验证结果
        $this->assertSame($shop, $result);
    }
    
    public function testFindByKdtId_withNonExistingShop(): void
    {
        // 配置模拟方法
        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['kdtId' => 99999])
            ->willReturn(null);
        
        // 执行被测方法
        $result = $this->repository->findByKdtId(99999);
        
        // 验证结果
        $this->assertNull($result);
    }
} 