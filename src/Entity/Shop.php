<?php

namespace YouzanApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use YouzanApiBundle\Repository\ShopRepository;

/**
 * 有赞店铺实体
 */
#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ORM\Table(name: 'ims_youzan_shop', options: ['comment' => '有赞店铺表'])]
class Shop
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\Column(type: Types::INTEGER, unique: true, options: ['comment' => '有赞店铺ID'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $kdtId;

    #[ORM\Column(type: Types::STRING, length: 64, options: ['comment' => '店铺名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 64)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '店铺Logo'])]
    #[Assert\Length(max: 255)]
    private ?string $logo = null;

    /** @var Collection<int, Account> */
    #[ORM\ManyToMany(targetEntity: Account::class, inversedBy: 'shops')]
    #[ORM\JoinTable(name: 'youzan_account_shop')]
    private Collection $accounts;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }

    public function getKdtId(): int
    {
        return $this->kdtId;
    }

    public function setKdtId(int $kdtId): void
    {
        $this->kdtId = $kdtId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->addShop($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): self
    {
        if ($this->accounts->removeElement($account)) {
            $account->removeShop($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }
}
