<?php
declare(strict_types=1);

namespace DomainShop\Infrastructure;

use Common\Persistence\Database;
use DomainShop\Domain\PricingRepository;
use DomainShop\Entity\Pricing;

class InMemoryPricingRepository implements PricingRepository
{
    private $pricings = [];

    public function retrieve(string $domainExtension): Pricing
    {
        if (! array_key_exists($domainExtension, $this->pricings)) {
            throw new \RuntimeException(sprintf('Pricing %s does not exist', $domainExtension));
        }

        return $this->pricings[$domainExtension];
    }

    public function persist(Pricing $pricing): void
    {
        $this->pricings[$pricing->id()] = $pricing;
    }
}
