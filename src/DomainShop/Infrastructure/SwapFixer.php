<?php

declare(strict_types=1);

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2018 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DomainShop\Infrastructure;
use DomainShop\Application\ExchangeRateProvider;
use DomainShop\Domain\Clock;
use Swap\Builder;

/**
 * @author Julian Prud'homme <julian.prudhomme@akeneo.com>
 */
class SwapFixer implements ExchangeRateProvider
{
    /** @var Clock */
    private $clock;

    public function __construct(Clock $clock)
    {
        $this->clock = $clock;
    }

    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $swap = (new Builder())
            ->add('fixer', ['access_key' => 'e495bd221c904b9155f76e130814d567'])
            ->build();
        $rate = $swap->historical(
            $fromCurrency . '/' . $toCurrency,
            $this->clock->getCurrentTime()
        );

        return (float) $rate->getValue();
    }
}
