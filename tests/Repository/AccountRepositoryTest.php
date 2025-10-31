<?php

namespace YouzanApiBundle\Tests\Repository;

use Doctrine\ORM\Exception\MissingIdentifierField;
use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    protected function onSetUp(): void
    {
    }

    protected function createNewEntity(): object
    {
        $entity = new Account();

        $entity->setName('Test Account ' . uniqid());
        $entity->setClientId('test_client_' . uniqid());
        $entity->setClientSecret('test_secret_' . uniqid());

        return $entity;
    }

    protected function getRepository(): AccountRepository
    {
        return self::getService(AccountRepository::class);
    }

    private function createTestAccount(?string $name = null, ?string $clientId = null, ?string $clientSecret = null): Account
    {
        $account = new Account();
        $account->setName($name ?? 'Test Account ' . uniqid());
        $account->setClientId($clientId ?? 'test_client_' . uniqid());
        $account->setClientSecret($clientSecret ?? 'test_secret_' . uniqid());

        $this->persistAndFlush($account);

        return $account;
    }

    public function testFindByClientIdWithExistingAccount(): void
    {
        $repository = $this->getRepository();
        $clientId = 'test_client_' . uniqid();

        $account = new Account();
        $account->setName('Test Account');
        $account->setClientId($clientId);
        $account->setClientSecret('test_client_secret');

        $entityManager = self::getEntityManager();
        $entityManager->persist($account);
        $entityManager->flush();

        $result = $repository->findByClientId($clientId);

        $this->assertNotNull($result);
        $this->assertEquals($clientId, $result->getClientId());
        $this->assertEquals('Test Account', $result->getName());
    }

    public function testFindByClientIdWithNonExistingAccount(): void
    {
        $repository = $this->getRepository();

        $result = $repository->findByClientId('non_existing_client_' . uniqid());

        $this->assertNull($result);
    }

    public function testBasicRepositoryOperations(): void
    {
        $repository = $this->getRepository();

        // 测试基本 find 操作
        $result = $repository->find(999999999999999);
        $this->assertNull($result);

        $result = $repository->find(0);
        $this->assertNull($result);

        // 测试 findBy 操作
        $results = $repository->findBy(['clientSecret' => 'non_existing_secret_' . uniqid()]);
        $this->assertIsArray($results);
        $this->assertEmpty($results);

        // 测试 findOneBy 操作
        $result = $repository->findOneBy(['clientId' => 'non_existing_client_' . uniqid()]);
        $this->assertNull($result);
    }

    public function testFindWithNullId(): void
    {
        $repository = $this->getRepository();

        $this->expectException(MissingIdentifierField::class);
        $repository->find(null);
    }

    public function testSaveAndRemoveOperations(): void
    {
        $repository = $this->getRepository();
        $entityManager = self::getEntityManager();

        // 测试保存操作
        $account = new Account();
        $account->setName('Test Save Account ' . uniqid());
        $account->setClientId('save_client_' . uniqid());
        $account->setClientSecret('save_secret_' . uniqid());

        // 测试不刷新的保存
        $repository->save($account, false);
        $this->assertTrue($entityManager->contains($account));

        $entityManager->flush();
        $this->assertEntityPersisted($account);

        $accountId = $account->getId();

        // 测试删除操作
        $managedAccount = $entityManager->find(Account::class, $accountId);
        $this->assertNotNull($managedAccount);

        $repository->remove($managedAccount, true);
        $this->assertEntityNotExists(Account::class, $accountId);
    }

    public function testOrderingAndNullQueries(): void
    {
        $repository = $this->getRepository();

        // 创建测试数据
        $uniqueId = uniqid();
        $account1 = $this->createTestAccount("A Account {$uniqueId}", "client_a_{$uniqueId}");
        $account2 = $this->createTestAccount("Z Account {$uniqueId}", "client_z_{$uniqueId}");

        // 测试按名称升序排列
        $result = $repository->findOneBy(['name' => "A Account {$uniqueId}"], ['name' => 'ASC']);
        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals("A Account {$uniqueId}", $result->getName());

        // 测试按名称降序排列
        $result = $repository->findOneBy(['name' => "Z Account {$uniqueId}"], ['name' => 'DESC']);
        $this->assertInstanceOf(Account::class, $result);
        $this->assertEquals("Z Account {$uniqueId}", $result->getName());

        // 测试null查询
        $results = $repository->findBy(['createTime' => null]);
        $this->assertIsArray($results);

        $results = $repository->findBy(['updateTime' => null]);
        $this->assertIsArray($results);

        $count = $repository->count(['createTime' => null]);
        $this->assertGreaterThanOrEqual(0, $count);

        $count = $repository->count(['updateTime' => null]);
        $this->assertGreaterThanOrEqual(0, $count);
    }

    public function testFindOneByWithNonExistentField(): void
    {
        $repository = $this->getRepository();

        $this->expectException(UnrecognizedField::class);
        $repository->findOneBy(['nonExistentField' => 'value']);
    }
}
