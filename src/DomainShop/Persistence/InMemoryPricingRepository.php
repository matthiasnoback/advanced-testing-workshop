<?php
declare(strict_types=1);

namespace DomainShop\Persistence;

use DomainShop\Entity\Pricing;
use DomainShop\Entity\PricingRepository;

final class InMemoryPricingRepository implements PricingRepository
{
    private $pricings = [];

    public function save(Pricing $pricing): void
    {
        $this->pricings[$pricing->id()] = $pricing;
    }

    public function getById(string $id): Pricing
    {
        return $this->pricings[$id];
    }
}
