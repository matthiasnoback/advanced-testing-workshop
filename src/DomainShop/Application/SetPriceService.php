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

namespace DomainShop\Application;
use Common\Persistence\Database;
use DomainShop\Domain\PricingRepository;
use DomainShop\Entity\Pricing;

/**
 * @author Julian Prud'homme <julian.prudhomme@akeneo.com>
 */
class SetPriceService
{
    /** @var PricingRepository */
    private $pricingRepository;

    public function __construct(PricingRepository $pricingRepository)
    {
        $this->pricingRepository = $pricingRepository;
    }

    public function __invoke(string $extension, string $currency, int $amount): void
    {
        try {
            $pricing = Database::retrieve(Pricing::class, $extension);
        } catch (\RuntimeException $exception) {
            $pricing = new Pricing();
            $pricing->setExtension($extension);
        }

        $pricing->setCurrency($currency);
        $pricing->setAmount($amount);

        $this->pricingRepository->persist($pricing);
    }
}
