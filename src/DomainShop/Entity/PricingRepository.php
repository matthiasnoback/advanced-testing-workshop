<?php
declare(strict_types=1);

namespace DomainShop\Entity;

interface PricingRepository
{
    public function save(Pricing $pricing): void;

    public function getById(string $id): Pricing;
}
