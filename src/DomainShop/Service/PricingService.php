<?php
declare(strict_types=1);

namespace DomainShop\Service;

use DomainShop\Entity\Price;
use DomainShop\Entity\PricingRepository;
use DomainShop\Service\ExchangeRate\ExchangeRateService;

final class PricingService
{
    /**
     * @var PricingRepository
     */
    private $pricingRepository;

    /**
     * @var ExchangeRateService
     */
    private $exchangeRateService;

    public function __construct(PricingRepository $pricingRepository, ExchangeRateService $exchangeRateService)
    {
        $this->pricingRepository = $pricingRepository;
        $this->exchangeRateService = $exchangeRateService;
    }

    public function getPriceForDomainNameExtension(string $domainNameExtension, string $payInCurrency): Price
    {
        $pricing = $this->pricingRepository->getById($domainNameExtension);

        if ($payInCurrency === $pricing->getCurrency()) {
            return new Price($pricing->getCurrency(), $pricing->getAmount());
        }

        $exchangeRate = $this->exchangeRateService->getExchangeRate($pricing->getCurrency(), $payInCurrency);

        return new Price($payInCurrency, (int)round($pricing->getAmount() * $exchangeRate));
    }
}
