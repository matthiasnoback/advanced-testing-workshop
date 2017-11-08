<?php
declare(strict_types=1);

namespace DomainShop\Service;

use DomainShop\Clock;
use Swap\Builder;

final class LiveExchangeRateService implements ExchangeRateService
{
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $swap = (new Builder())
            ->add('fixer')
            ->build();

        $rate = $swap->historical(
            $fromCurrency . '/' . $toCurrency,
            $this->clock->now()
        );

        return (float)$rate->getValue();
    }
}
