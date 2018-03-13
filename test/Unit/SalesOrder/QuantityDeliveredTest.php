<?php
declare(strict_types=1);

namespace SalesOrder;

final class QuantityDeliveredTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_cant_be_negative()
    {
        $this->expectException(\InvalidArgumentException::class);

        new QuantityDelivered(-1);
    }

    /**
     * @test
     */
    public function it_can_be_0()
    {
        $quantity = new QuantityDelivered(0);

        self::assertEquals(0, $quantity->asFloat());
    }

    /**
     * @test
     */
    public function it_can_be_positive()
    {
        $quantity = new QuantityDelivered(2);

        self::assertEquals(2, $quantity->asFloat());
    }
}
