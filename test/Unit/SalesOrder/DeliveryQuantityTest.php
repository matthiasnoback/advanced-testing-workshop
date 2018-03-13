<?php
declare(strict_types=1);

namespace SalesOrder;

final class DeliveryQuantityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_cant_be_negative()
    {
        $this->expectException(\InvalidArgumentException::class);

        new DeliveryQuantity(-1);
    }

    /**
     * @test
     */
    public function it_cant_be_0()
    {
        $this->expectException(\InvalidArgumentException::class);

        new DeliveryQuantity(0);
    }

    /**
     * @test
     */
    public function it_can_be_positive()
    {
        $quantity = new DeliveryQuantity(2);

        self::assertEquals(2, $quantity->asFloat());
    }
}
