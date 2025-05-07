<?php

namespace YouzanApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiBundle\Entity\Shop;

/**
 * @method Shop|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shop|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shop[] findAll()
 * @method Shop[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
} 