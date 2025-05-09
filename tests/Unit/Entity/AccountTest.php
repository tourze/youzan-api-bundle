<?php

namespace YouzanApiBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;

class AccountTest extends TestCase
{
    public function testCreateEmptyAccount(): void
    {
        $account = new Account();
        
        $this->assertNull($account->getId());
        $this->assertInstanceOf(ArrayCollection::class, $account->getShops());
        $this->assertEmpty($account->getShops());
        $this->assertNull($account->getCreateTime());
        $this->assertNull($account->getUpdateTime());
    }
    
    public function testSetAndGetName(): void
    {
        $account = new Account();
        $testName = 'Test Account Name';
        
        $this->assertSame($account, $account->setName($testName));
        $this->assertSame($testName, $account->getName());
    }
    
    public function testSetAndGetClientId(): void
    {
        $account = new Account();
        $testClientId = 'test_client_id_123';
        
        $this->assertSame($account, $account->setClientId($testClientId));
        $this->assertSame($testClientId, $account->getClientId());
    }
    
    public function testSetAndGetClientSecret(): void
    {
        $account = new Account();
        $testClientSecret = 'test_client_secret_456';
        
        $this->assertSame($account, $account->setClientSecret($testClientSecret));
        $this->assertSame($testClientSecret, $account->getClientSecret());
    }
    
    public function testAddAndRemoveShop(): void
    {
        $account = new Account();
        $shop = new Shop();
        
        // 测试添加商店
        $this->assertSame($account, $account->addShop($shop));
        $this->assertTrue($account->getShops()->contains($shop));
        $this->assertCount(1, $account->getShops());
        
        // 测试重复添加相同商店不会导致重复
        $account->addShop($shop);
        $this->assertCount(1, $account->getShops());
        
        // 测试移除商店
        $this->assertSame($account, $account->removeShop($shop));
        $this->assertFalse($account->getShops()->contains($shop));
        $this->assertCount(0, $account->getShops());
    }
    
    public function testSetAndGetCreateTime(): void
    {
        $account = new Account();
        $dateTime = new \DateTime('2023-05-15 10:00:00');
        
        $account->setCreateTime($dateTime);
        $this->assertSame($dateTime, $account->getCreateTime());
    }
    
    public function testSetAndGetUpdateTime(): void
    {
        $account = new Account();
        $dateTime = new \DateTime('2023-05-15 11:30:00');
        
        $account->setUpdateTime($dateTime);
        $this->assertSame($dateTime, $account->getUpdateTime());
    }
    
    public function testFluentInterface(): void
    {
        $account = new Account();
        $result = $account
            ->setName('Test Account')
            ->setClientId('client_id_123')
            ->setClientSecret('client_secret_456');
        
        $this->assertSame($account, $result);
        $this->assertSame('Test Account', $account->getName());
        $this->assertSame('client_id_123', $account->getClientId());
        $this->assertSame('client_secret_456', $account->getClientSecret());
    }
} 