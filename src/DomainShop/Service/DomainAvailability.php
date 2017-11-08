<?php
declare(strict_types=1);

namespace DomainShop\Service;

final class DomainAvailability
{
    /**
     * @var bool
     */
    private $isAvailable;

    /**
     * @var string
     */
    private $whoisInformation;

    public function __construct(bool $isAvailable, string $whoisInformation)
    {
        $this->isAvailable = $isAvailable;
        $this->whoisInformation = $whoisInformation;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function whoisInformation(): string
    {
        return $this->whoisInformation;
    }
}
