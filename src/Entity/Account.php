<?php

namespace YouzanApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Repository\AccountRepository;

/**
 * 有赞账号实体
 */
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'ims_youzan_account', options: ['comment' => '有赞账号表'])]
class Account
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '账号名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '客户端ID'])]
    private string $clientId;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '客户端密钥'])]
    private string $clientSecret;

    /** @var Collection<int, Shop> */
    #[ORM\ManyToMany(targetEntity: Shop::class, mappedBy: 'accounts')]
    private Collection $shops;

    public function __construct()
    {
        $this->shops = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    public function addShop(Shop $shop): self
    {
        if (!$this->shops->contains($shop)) {
            $this->shops->add($shop);
        }

        return $this;
    }

    public function removeShop(Shop $shop): self
    {
        $this->shops->removeElement($shop);
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
