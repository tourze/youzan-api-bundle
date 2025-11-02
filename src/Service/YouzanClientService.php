<?php

namespace YouzanApiBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Youzan\Open\Client;
use Youzan\Open\Token;
use YouzanApiBundle\Entity\Account;
use YouzanApiBundle\Exception\AccessTokenException;
use YouzanApiBundle\Repository\AccountRepository;

/**
 * 有赞客户端服务
 */
#[Autoconfigure(public: true)]
class YouzanClientService
{
    /** @var array<string, Client> */
    private array $clientCache = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountRepository $accountRepository,
        private readonly string $defaultKdtId = '',
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
        try {
            $resp = (new Token($clientId, $account->getClientSecret()))->getSelfAppToken($this->defaultKdtId);
        } catch (\Throwable $e) {
            throw new AccessTokenException('Failed to get access token from Youzan API', 0, $e);
        }

        if (!\is_array($resp) || !isset($resp['access_token'])) {
            throw new AccessTokenException('Failed to get access token from Youzan API');
        }

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
        if (null === $account) {
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
        $account->setName($name);
        $account->setClientId($clientId);
        $account->setClientSecret($clientSecret);

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }
}
