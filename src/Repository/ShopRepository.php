<?php

namespace YouzanApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;
use YouzanApiBundle\Entity\Shop;

/**
 * 有赞店铺仓库类
 *
 * @extends ServiceEntityRepository<Shop>
 */
#[AsRepository(entityClass: Shop::class)]
class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    /**
     * 根据有赞店铺ID查找店铺
     */
    public function findByKdtId(int $kdtId): ?Shop
    {
        return $this->findOneBy(['kdtId' => $kdtId]);
    }

    /**
     * 保存店铺实体
     */
    public function save(Shop $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 删除店铺实体
     */
    public function remove(Shop $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
