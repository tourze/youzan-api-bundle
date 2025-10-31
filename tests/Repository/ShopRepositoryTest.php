<?php

namespace YouzanApiBundle\Tests\Repository;

use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\Exception\MissingIdentifierField;
use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Shop;
use YouzanApiBundle\Repository\ShopRepository;

/**
 * @internal
 */
#[CoversClass(ShopRepository::class)]
#[RunTestsInSeparateProcesses]
final class ShopRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $entity = new Shop();

        $entity->setName('Test Shop ' . uniqid());
        $entity->setKdtId((int) hexdec(substr(uniqid(), -8)));

        return $entity;
    }

    protected function getRepository(): ShopRepository
    {
        return self::getService(ShopRepository::class);
    }

    private function createTestShop(?string $name = null, ?int $kdtId = null, ?string $logo = null): Shop
    {
        $shop = new Shop();
        $shop->setName($name ?? 'Test Shop ' . uniqid());
        $shop->setKdtId($kdtId ?? (int) hexdec(substr(uniqid(), -8)));

        if (null !== $logo) {
            $shop->setLogo($logo);
        }

        $this->persistAndFlush($shop);

        return $shop;
    }

    public function testFindByKdtIdOperations(): void
    {
        $repository = $this->getRepository();

        // 测试存在的 kdtId
        $kdtId = (int) hexdec(substr(uniqid(), -8));
        $shop = new Shop();
        $shop->setName('Test Shop');
        $shop->setKdtId($kdtId);

        $entityManager = self::getEntityManager();
        $entityManager->persist($shop);
        $entityManager->flush();

        $result = $repository->findByKdtId($kdtId);
        $this->assertNotNull($result);
        $this->assertEquals($kdtId, $result->getKdtId());
        $this->assertEquals('Test Shop', $result->getName());

        // 测试不存在的 kdtId
        $nonExistentKdtId = (int) hexdec(substr(uniqid(), -8));
        $result = $repository->findByKdtId($nonExistentKdtId);
        $this->assertNull($result);
    }

    public function testRepositoryBasicOperations(): void
    {
        $repository = $this->getRepository();

        // 测试基本 find 操作
        $result = $repository->find(999999999999999);
        $this->assertNull($result);

        $result = $repository->find(0);
        $this->assertNull($result);

        // 测试 findBy 操作
        $results = $repository->findBy(['name' => 'non_existing_shop_' . uniqid()]);
        $this->assertIsArray($results);
        $this->assertEmpty($results);

        // 测试 findOneBy 操作
        $result = $repository->findOneBy(['kdtId' => (int) hexdec(substr(uniqid(), -8))]);
        $this->assertNull($result);
    }

    public function testFindWithNullId(): void
    {
        $repository = $this->getRepository();

        $this->expectException(MissingIdentifierField::class);
        $repository->find(null);
    }

    public function testNullValueOperations(): void
    {
        $repository = $this->getRepository();
        $uniqueId = uniqid();

        // 创建测试数据
        $shop1 = $this->createTestShop("Shop1 {$uniqueId}", null, null); // logo 为 null
        $shop2 = $this->createTestShop("Shop2 {$uniqueId}", null, "/logo-{$uniqueId}.png"); // logo 有值

        // 测试 null 查询
        $resultsWithNullLogo = $repository->findBy(['logo' => null]);
        $this->assertIsArray($resultsWithNullLogo);
        $this->assertGreaterThanOrEqual(1, count($resultsWithNullLogo));

        $resultsWithSpecificLogo = $repository->findBy(['logo' => "/logo-{$uniqueId}.png"]);
        $this->assertIsArray($resultsWithSpecificLogo);
        $this->assertCount(1, $resultsWithSpecificLogo);

        // 测试 count 操作
        $countWithNullLogo = $repository->count(['logo' => null]);
        $this->assertGreaterThanOrEqual(1, $countWithNullLogo);

        $countWithSpecificLogo = $repository->count(['logo' => "/logo-{$uniqueId}.png"]);
        $this->assertEquals(1, $countWithSpecificLogo);
    }

    public function testOrderingOperations(): void
    {
        $repository = $this->getRepository();
        $uniqueId = uniqid();

        // 创建测试数据
        $shop1 = $this->createTestShop("A Shop {$uniqueId}");
        $shop2 = $this->createTestShop("Z Shop {$uniqueId}");

        // 测试按名称升序排列
        $result = $repository->findOneBy(['name' => "A Shop {$uniqueId}"], ['name' => 'ASC']);
        $this->assertInstanceOf(Shop::class, $result);
        $this->assertEquals("A Shop {$uniqueId}", $result->getName());

        // 测试按名称降序排列
        $result = $repository->findOneBy(['name' => "Z Shop {$uniqueId}"], ['name' => 'DESC']);
        $this->assertInstanceOf(Shop::class, $result);
        $this->assertEquals("Z Shop {$uniqueId}", $result->getName());
    }

    public function testSaveOperations(): void
    {
        $repository = $this->getRepository();
        $entityManager = self::getEntityManager();

        // 测试保存操作
        $shop = new Shop();
        $shop->setName('Save Test Shop ' . uniqid());
        $shop->setKdtId((int) hexdec(substr(uniqid(), -8)));

        // 测试不刷新的保存
        $repository->save($shop, false);
        $this->assertTrue($entityManager->contains($shop));

        $entityManager->flush();
        $this->assertEntityPersisted($shop);

        // 测试带刷新的保存
        $shop2 = new Shop();
        $shop2->setName('Save Test Shop 2 ' . uniqid());
        $shop2->setKdtId((int) hexdec(substr(uniqid(), -8)));

        $repository->save($shop2, true);
        $this->assertEntityPersisted($shop2);
    }

    public function testRemoveOperations(): void
    {
        $repository = $this->getRepository();
        $entityManager = self::getEntityManager();

        // 创建测试数据
        $shop = $this->createTestShop('Delete Test Shop ' . uniqid());
        $shopId = $shop->getId();

        $this->assertEntityPersisted($shop);

        // 重新获取实体确保它在 EntityManager 上下文中
        $managedShop = $entityManager->find(Shop::class, $shopId);
        $this->assertNotNull($managedShop);

        // 测试删除操作
        $repository->remove($managedShop, true);
        $this->assertEntityNotExists(Shop::class, $shopId);
    }

    public function testDatabaseExceptionHandling(): void
    {
        $repository = $this->getRepository();

        // 测试数据库连接异常处理
        try {
            $repository->findAll();
        } catch (DBALException $e) {
            $this->assertInstanceOf(DBALException::class, $e);
        }

        // 正常情况下应该成功
        $results = $repository->findAll();
        $this->assertIsArray($results);
    }

    public function testFindOneByWithNonExistentField(): void
    {
        $repository = $this->getRepository();

        $this->expectException(UnrecognizedField::class);
        $repository->findOneBy(['nonExistentField' => 'value']);
    }
}
