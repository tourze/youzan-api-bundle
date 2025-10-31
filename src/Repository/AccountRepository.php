<?php

namespace YouzanApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiBundle\Entity\Account;

/**
 * 有赞账号仓库类
 *
 * @extends ServiceEntityRepository<Account>
 */
#[AsRepository(entityClass: Account::class)]
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * 根据客户端ID查询账号
     */
    public function findByClientId(string $clientId): ?Account
    {
        return $this->findOneBy(['clientId' => $clientId]);
    }

    /**
     * 保存账号实体
     */
    public function save(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除账号实体
     */
    public function remove(Account $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
