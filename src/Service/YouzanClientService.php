<?php

namespace YouzanApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Youzan\Open\Client;
use Youzan\Open\Token;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Repository\AccountRepository;

/**
 * 有赞客户端服务
 */
class YouzanClientService
{
    private array $clientCache = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository
    ) {
    }

    /**
     * 获取有赞客户端
     */
    public function getClient(Account $account): Client
    {
        $clientId = $account->getClientId();

        // 从缓存获取客户端
        if (isset($this->clientCache[$clientId])) {
            return $this->clientCache[$clientId];
        }

        // 创建新客户端
        $resp = (new Token($clientId, $account->getClientSecret()))->getSelfAppToken('YOUR_KDT_ID');
        $client = new Client($resp['access_token']);

        // 缓存客户端
        $this->clientCache[$clientId] = $client;

        return $client;
    }

    /**
     * 根据客户端ID获取客户端
     */
    public function getClientByClientId(string $clientId): ?Client
    {
        $account = $this->accountRepository->findByClientId($clientId);
        if (!$account) {
            return null;
        }

        return $this->getClient($account);
    }

    /**
     * 创建账号
     */
    public function createAccount(string $name, string $clientId, string $clientSecret): Account
    {
        $account = new Account();
        $account->setName($name)
            ->setClientId($clientId)
            ->setClientSecret($clientSecret);

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }
}
