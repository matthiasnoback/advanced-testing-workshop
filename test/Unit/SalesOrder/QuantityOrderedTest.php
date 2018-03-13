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

    /**
     * @test
     */
    public function it_can_tell_how_much_is_open_based_on_the_quantity_delivered()
    {
        $quantityOrdered = new QuantityOrdered(3);

        $quantityDelivered = new QuantityDelivered(2);

        self::assertEquals(
            new QuantityOpen(1),
            $quantityOrdered->calculateQuantityOpen($quantityDelivered)
        );
    }

    /**
     * @test
     */
    public function quantity_open_will_be_0_in_case_of_over_delivery()
    {
        $quantityOrdered = new QuantityOrdered(2);

        $quantityDelivered = new QuantityDelivered(3);

        self::assertEquals(
            new QuantityOpen(0),
            $quantityOrdered->calculateQuantityOpen($quantityDelivered)
        );
    }
}
