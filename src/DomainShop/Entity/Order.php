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
     * @var Price
     */
    private $price;

    /**
     * @var string
     */
    private $payInCurrency;

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

    public function getDomainNameExtension(): string
    {
        $parts = explode('.', $this->getDomainName());
        return '.' . $parts[1];
    }

    public function setPayInCurrency(string $currency): void
    {
        $this->payInCurrency = $currency;
    }

    public function getPayInCurrency(): string
    {
        return $this->payInCurrency;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function pay(string $currency, int $amount): void
    {
        if ($this->getPrice()->currency() !== $currency) {
            throw new \LogicException(sprintf(
                'Order should be paid in the currency "%s"',
                $this->getPrice()->currency()
            ));
        }

        if ($this->getPrice()->amount() !== $amount) {
            throw new \LogicException(sprintf(
                'Paid amount should be "%d"',
                $this->getPrice()->amount()
            ));
        }

        $this->wasPaid = true;
    }
}
