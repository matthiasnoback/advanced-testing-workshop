<?php
declare(strict_types=1);

namespace DomainShop;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
