<?php
/**
 * Created by PhpStorm.
 * User: phil
 * Date: 11/10/2018
 * Time: 11:18
 */

namespace DomainShop\Domain;

use DomainShop\Entity\Pricing;

interface PricingRepository
{
    public function retrieve(string $domainNameExtension): Pricing;

    public function persist(Pricing $pricing): void;
}
