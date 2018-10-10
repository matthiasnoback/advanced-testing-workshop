<?php
declare(strict_types=1);

namespace DomainShop\Domain;

interface Clock
{
    public function getCurrentTime(): \DateTimeImmutable;
}
