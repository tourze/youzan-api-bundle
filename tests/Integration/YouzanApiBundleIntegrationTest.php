<?php

namespace YouzanApiBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Entity\Shop;
use YouzanApiBundle\Repository\AccountRepository;
use YouzanApiBundle\Repository\ShopRepository;
use YouzanApiBundle\Service\YouzanClientService;

/**
 * 测试YouzanApiBundle与Symfony框架的集成
 */
class YouzanApiBundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        // 启动内核
        self::bootKernel();
        $container = static::getContainer();

        // 获取实体管理器
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);

        // 创建/更新数据库模式
        $schemaTool = new SchemaTool($entityManager);
        $metadatas = $entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    public function testBundleServicesRegistration(): void
    {
        $container = static::getContainer();

        // 验证服务是否已正确注册
        $this->assertTrue($container->has(YouzanClientService::class));
        $this->assertTrue($container->has(AccountRepository::class));
        $this->assertTrue($container->has(ShopRepository::class));
        
        // 验证服务实例是否可以获取
        $this->assertInstanceOf(YouzanClientService::class, $container->get(YouzanClientService::class));
        $this->assertInstanceOf(AccountRepository::class, $container->get(AccountRepository::class));
        $this->assertInstanceOf(ShopRepository::class, $container->get(ShopRepository::class));
    }

    public function testEntityPersistence(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);
        $accountRepository = $container->get(AccountRepository::class);

        // 创建测试账号
        $account = new Account();
        $account->setName('Integration Test Account')
            ->setClientId('integration_client_id')
            ->setClientSecret('integration_client_secret');

        // 保存到数据库
        $entityManager->persist($account);
        $entityManager->flush();
        $entityManager->clear();

        // 验证是否已持久化并可以检索
        $foundAccount = $accountRepository->findByClientId('integration_client_id');
        $this->assertNotNull($foundAccount);
        $this->assertInstanceOf(Account::class, $foundAccount);
        $this->assertEquals('Integration Test Account', $foundAccount->getName());
        $this->assertEquals('integration_client_id', $foundAccount->getClientId());
    }

    public function testEntityRelationships(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);

        // 创建测试账号和商店
        $account = new Account();
        $account->setName('Relationship Test Account')
            ->setClientId('relationship_client_id')
            ->setClientSecret('relationship_client_secret');

        $shop = new Shop();
        $shop->setName('Relationship Test Shop')
            ->setKdtId('relationship_kdt_id')
            ->addAccount($account);

        // 保存到数据库
        $entityManager->persist($account);
        $entityManager->persist($shop);
        $entityManager->flush();
        $entityManager->clear();

        // 验证关系是否正确保存
        $shopRepository = $container->get(ShopRepository::class);
        $foundShop = $shopRepository->findByKdtId('relationship_kdt_id');
        
        $this->assertNotNull($foundShop);
        $this->assertEquals('Relationship Test Shop', $foundShop->getName());
        
        // 验证关联的账号
        $this->assertCount(1, $foundShop->getAccounts());
        $relatedAccount = $foundShop->getAccounts()->first();
        $this->assertEquals('Relationship Test Account', $relatedAccount->getName());
        $this->assertEquals('relationship_client_id', $relatedAccount->getClientId());
    }

    public function testYouzanClientService(): void
    {
        $container = static::getContainer();
        $entityManager = $container->get('doctrine.orm.entity_manager');
        assert($entityManager instanceof EntityManagerInterface);
        $youzanClientService = $container->get(YouzanClientService::class);

        // 使用服务创建账号
        $account = $youzanClientService->createAccount(
            'Service Test Account',
            'service_client_id',
            'service_client_secret'
        );

        // 验证账号是否已创建
        $this->assertNotNull($account->getId());
        $this->assertEquals('Service Test Account', $account->getName());
        
        // 验证是否可以获取客户端
        $client = $youzanClientService->getClient($account);
        $this->assertNotNull($client);
        
        // 验证通过客户端ID获取客户端
        $clientByClientId = $youzanClientService->getClientByClientId('service_client_id');
        $this->assertNotNull($clientByClientId);
    }
} 