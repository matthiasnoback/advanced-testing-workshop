<?php
declare(strict_types=1);

namespace Test\Integration;

use DomainShop\Domain\PricingRepository;
use DomainShop\Entity\Pricing;
use DomainShop\Infrastructure\FileSystemPricingRepository;
use DomainShop\Infrastructure\InMemoryPricingRepository;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

final class PricingRepositoryIntegrationTest extends TestCase
{
    /**
     * @test
     * @dataProvider repositoryProvider
     */
    public function it_retrieves_a_pricing(PricingRepository $repository)
    {
        $pricing = new Pricing();
        $pricing->setExtension('.com');
        $pricing->setAmount(666);
        $pricing->setCurrency('USD');

        $repository->persist($pricing);

        $foundPricing = $repository->retrieve('.com');

        Assert::assertEquals($pricing, $foundPricing);
    }

    /**
     * @test
     * @dataProvider repositoryProvider
     */
    public function it_throws_an_exception_if_pricing_is_not_found(PricingRepository $repository)
    {
        $this->expectException(\RuntimeException::class);

        $repository->retrieve('.unknown');
    }

    public function repositoryProvider()
    {
        return [
            [new FileSystemPricingRepository()],
            [new InMemoryPricingRepository()],
        ];
    }
}
