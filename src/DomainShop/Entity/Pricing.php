<?php
declare(strict_types=1);

namespace DomainShop\Entity;

final class Pricing
{
    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var int
     */
    private $amount;

    public function id(): string
    {
        return $this->extension;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
