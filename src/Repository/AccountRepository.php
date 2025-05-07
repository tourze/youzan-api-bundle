<?php

namespace YouzanApiBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use YouzanApiBundle\Entity\Account;

/**
 * 有赞账号仓库类
 *
 * @method Account|null find($id, $lockMode = null, $lockVersion = null)
 * @method Account|null findOneBy(array $criteria, array $orderBy = null)
 * @method Account[] findAll()
 * @method Account[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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
}
