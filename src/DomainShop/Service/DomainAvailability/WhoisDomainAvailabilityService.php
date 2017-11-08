<?php
declare(strict_types=1);

namespace DomainShop\Service\DomainAvailability;

use Novutec\WhoisParser\Parser;

final class WhoisDomainAvailabilityService implements DomainAvailabilityService
{
    public function lookup($domainName): DomainAvailability
    {
        if (!preg_match('/^\w+\.\w+$/', $domainName)) {
            throw new \RuntimeException('Invalid domain name provided');
        }

        $parser = new Parser();
        $parser->throwExceptions(true);
        $result = $parser->lookup($domainName);
        if ($result->name !== $domainName) {
            throw new \RuntimeException('Invalid domain name');
        }

        $isAvailable = !$result->registered;
        $whoisInformation = implode("\n", $result->rawdata);

        return new DomainAvailability($isAvailable, $whoisInformation);
    }
}
