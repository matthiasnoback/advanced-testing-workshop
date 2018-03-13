<?php
declare(strict_types=1);

namespace SalesOrder;

final class QuantityOrderedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_cant_be_negative()
    {
        $this->expectException(\InvalidArgumentException::class);

        new QuantityOrdered(-1);
    }

    /**
     * @test
     */
    public function it_cant_be_0()
    {
        $this->expectException(\InvalidArgumentException::class);

        new QuantityOrdered(0);
    }

    /**
     * @test
     */
    public function it_can_be_positive()
    {
        $quantityOrdered = new QuantityOrdered(2);

        self::assertEquals(2, $quantityOrdered->asFloat());
    }
}
