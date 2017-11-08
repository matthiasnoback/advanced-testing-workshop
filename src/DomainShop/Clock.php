<?php
declare(strict_types=1);

namespace DomainShop;

final class Clock
{
    /**
     * @var null|\DateTimeImmutable
     */
    private static $now;

    public static function setNow(\DateTimeImmutable $now): void
    {
        self::$now = $now;
    }

    public function now(): \DateTimeImmutable
    {
        return self::$now ?? new \DateTimeImmutable('now');
    }
}
