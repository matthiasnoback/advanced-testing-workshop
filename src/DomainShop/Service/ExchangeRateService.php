<?php
declare(strict_types=1);

namespace DomainShop\Service;

interface ExchangeRateService
{
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float;
}
