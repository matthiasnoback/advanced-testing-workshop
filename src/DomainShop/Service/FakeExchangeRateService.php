<?php
declare(strict_types=1);

namespace DomainShop\Service;

final class FakeExchangeRateService implements ExchangeRateService
{
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        return 1.156;
    }
}
