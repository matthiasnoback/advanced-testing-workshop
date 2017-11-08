<?php
declare(strict_types=1);

namespace DomainShop\Service\DomainAvailability;

interface DomainAvailabilityService
{
    public function lookup($domainName): DomainAvailability;
}
