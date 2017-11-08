<?php
declare(strict_types=1);

namespace DomainShop\Persistence;

use Common\Persistence\Database;
use DomainShop\Entity\Pricing;
use DomainShop\Entity\PricingRepository;

final class FileBasedPricingRepository implements PricingRepository
{
    public function save(Pricing $pricing): void
    {
        Database::persist($pricing);
    }

    public function getById(string $id): Pricing
    {
        return Database::retrieve(Pricing::class, $id);
    }
}
