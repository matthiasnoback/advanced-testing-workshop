<?php
declare(strict_types=1);

namespace DomainShop\Clock;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
