<?php
declare(strict_types=1);

namespace DomainShop\Entity;

final class Order
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $domainName;

    /**
     * @var string
     */
    private $ownerName;

    /**
     * @var string
     */
    private $ownerEmailAddress;

    /**
     * @var bool
     */
    private $wasPaid = false;

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    public function setDomainName(string $domainName): void
    {
        $this->domainName = $domainName;
    }

    public function getOwnerName(): string
    {
        return $this->ownerName;
    }

    public function setOwnerName(string $ownerName): void
    {
        $this->ownerName = $ownerName;
    }

    public function getOwnerEmailAddress(): string
    {
        return $this->ownerEmailAddress;
    }

    public function setOwnerEmailAddress(string $ownerEmailAddress): void
    {
        $this->ownerEmailAddress = $ownerEmailAddress;
    }

    public function wasPaid(): bool
    {
        return $this->wasPaid;
    }

    public function setWasPaid(bool $wasPaid): void
    {
        $this->wasPaid = $wasPaid;
    }
}
