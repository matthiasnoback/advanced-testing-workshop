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

use Assert\Assertion;
use DomainShop\Application\ExchangeRateProvider;

/**
 * @author Julian Prud'homme <julian.prudhomme@akeneo.com>
 */
class FixedExchangeRate implements ExchangeRateProvider
{
    public function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $rates = [
            'EUR/USD' => 1.151,
        ];

        $key = $fromCurrency . '/' . $toCurrency;
        Assertion::keyExists($rates, $key, 'Missing currency exchange rate');

        return $rates[$key];
    }
}
