<?php
declare(strict_types=1);

namespace DomainShop\Entity;

final class Price
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var int
     */
    private $amount;

    public function __construct(string $currency, int $amount)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
