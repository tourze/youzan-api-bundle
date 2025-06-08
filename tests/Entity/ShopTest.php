<?php

namespace YouzanApiBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

class ShopTest extends TestCase
{
    public function testCreateEmptyShop(): void
    {
        $shop = new Shop();
        
        $this->assertNull($shop->getId());
        $this->assertInstanceOf(ArrayCollection::class, $shop->getAccounts());
        $this->assertEmpty($shop->getAccounts());
        $this->assertNull($shop->getCreateTime());
        $this->assertNull($shop->getUpdateTime());
    }
    
    public function testSetAndGetName(): void
    {
        $shop = new Shop();
        $testName = 'Test Shop Name';
        
        $this->assertSame($shop, $shop->setName($testName));
        $this->assertSame($testName, $shop->getName());
    }
    
    public function testSetAndGetKdtId(): void
    {
        $shop = new Shop();
        $testKdtId = 123456;
        
        $this->assertSame($shop, $shop->setKdtId($testKdtId));
        $this->assertSame($testKdtId, $shop->getKdtId());
    }
    
    public function testAddAndRemoveAccount(): void
    {
        $shop = new Shop();
        $account = new Account();
        
        // 测试添加账号
        $this->assertSame($shop, $shop->addAccount($account));
        $this->assertTrue($shop->getAccounts()->contains($account));
        $this->assertCount(1, $shop->getAccounts());
        
        // 测试账号也应该添加了商店
        $this->assertTrue($account->getShops()->contains($shop));
        
        // 测试重复添加相同账号不会导致重复
        $shop->addAccount($account);
        $this->assertCount(1, $shop->getAccounts());
        
        // 测试移除账号
        $this->assertSame($shop, $shop->removeAccount($account));
        $this->assertFalse($shop->getAccounts()->contains($account));
        $this->assertCount(0, $shop->getAccounts());
        
        // 测试账号也应该移除了商店
        $this->assertFalse($account->getShops()->contains($shop));
    }
    
    public function testSetAndGetCreateTime(): void
    {
        $shop = new Shop();
        $dateTime = new \DateTime('2023-05-15 10:00:00');
        
        $shop->setCreateTime($dateTime);
        $this->assertSame($dateTime, $shop->getCreateTime());
    }
    
    public function testSetAndGetUpdateTime(): void
    {
        $shop = new Shop();
        $dateTime = new \DateTime('2023-05-15 11:30:00');
        
        $shop->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $shop->getUpdateTime());
    }
    
    public function testFluentInterface(): void
    {
        $shop = new Shop();
        $result = $shop
            ->setName('Test Shop')
            ->setKdtId(123456);
        
        $this->assertSame($shop, $result);
        $this->assertSame('Test Shop', $shop->getName());
        $this->assertSame(123456, $shop->getKdtId());
    }
    
    public function testAddMultipleAccounts(): void
    {
        $shop = new Shop();
        $account1 = new Account();
        $account1->setName('Account 1');
        
        $account2 = new Account();
        $account2->setName('Account 2');
        
        // 添加两个账号
        $shop->addAccount($account1);
        $shop->addAccount($account2);
        
        // 验证商店有两个账号
        $this->assertCount(2, $shop->getAccounts());
        $this->assertTrue($shop->getAccounts()->contains($account1));
        $this->assertTrue($shop->getAccounts()->contains($account2));
        
        // 验证两个账号都有这个商店
        $this->assertTrue($account1->getShops()->contains($shop));
        $this->assertTrue($account2->getShops()->contains($shop));
    }
} 