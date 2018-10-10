<?php
declare(strict_types=1);

namespace DomainShop\Infrastructure;

use DomainShop\Domain\Clock;

class SystemClock implements Clock
{
    /**
     * @return \DateTimeImmutable
     * @throws \Exception
     */
    public function getCurrentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
