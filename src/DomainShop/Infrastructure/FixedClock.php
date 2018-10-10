<?php
declare(strict_types=1);

namespace DomainShop\Infrastructure;

use DomainShop\Domain\Clock;

class FixedClock implements Clock
{
    /**
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    public function getCurrentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('2018-10-09 11:41:00');
    }
}
