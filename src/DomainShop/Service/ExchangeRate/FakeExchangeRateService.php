<?php
declare(strict_types=1);

namespace DomainShop\Service\ExchangeRate;

final class FakeExchangeRateService implements ExchangeRateService
{
    private $rates = [
        'EUR' => [
            'USD' => 1.156
        ]
    ];

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        return $this->rates[$fromCurrency][$toCurrency] ?? 1;
    }

    public function setExchangeRate(string $fromCurrency, string $toCurrency, float $amount)
    {
        $this->rates[$fromCurrency][$toCurrency] = $amount;
    }
}
