<?php
declare(strict_types=1);

namespace DomainShop\Service\DomainAvailability;

final class FakeDomainAvailabilityService implements DomainAvailabilityService
{
    public function lookup($domainName): DomainAvailability
    {
        if ($domainName === 'totallyrandomdomainname.com') {
            return new DomainAvailability(true, '');
        }

        return new DomainAvailability(false, '');
    }
}
