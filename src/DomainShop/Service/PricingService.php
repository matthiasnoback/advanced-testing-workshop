<?php
declare(strict_types=1);

namespace DomainShop\Service;

use Common\Persistence\Database;
use DomainShop\Entity\Price;
use DomainShop\Entity\Pricing;

final class PricingService
{
    /**
     * @var ExchangeRateService
     */
    private $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function getPriceForDomainNameExtension(string $domainNameExtension, string $payInCurrency): Price
    {
        /** @var Pricing $pricing */
        $pricing = Database::retrieve(Pricing::class, $domainNameExtension);

        if ($payInCurrency === $pricing->getCurrency()) {
            return new Price($pricing->getCurrency(), $pricing->getAmount());
        }

        $exchangeRate = $this->exchangeRateService->getExchangeRate($pricing->getCurrency(), $payInCurrency);

        return new Price($payInCurrency, (int)round($pricing->getAmount() * $exchangeRate));
    }
}
