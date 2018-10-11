<?php
declare(strict_types=1);

namespace DomainShop\Infrastructure;

use Common\Persistence\Database;
use DomainShop\Domain\PricingRepository;
use DomainShop\Entity\Pricing;

class FileSystemPricingRepository implements PricingRepository
{
    public function retrieve(string $domainNameExtension): Pricing
    {
        return Database::retrieve(Pricing::class, $domainNameExtension);
    }

    public function persist(Pricing $pricing): void
    {
        Database::persist($pricing);
    }
}
