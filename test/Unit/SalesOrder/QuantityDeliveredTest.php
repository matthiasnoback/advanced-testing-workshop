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

    /**
     * @test
     */
    public function you_can_add_a_delivery_quantity_to_it()
    {
        $quantity = new QuantityDelivered(2);

        $newQuantity = $quantity->add(new DeliveryQuantity(3));

        self::assertEquals(5, $newQuantity->asFloat());
    }

    /**
     * @test
     */
    public function you_can_subtract_a_delivery_quantity_to_it()
    {
        $quantity = new QuantityDelivered(5);

        $newQuantity = $quantity->subtract(new DeliveryQuantity(3));

        self::assertEquals(2, $newQuantity->asFloat());
    }

    /**
     * @test
     */
    public function the_minimum_quantity_after_subtraction_will_still_be_0()
    {
        $quantity = new QuantityDelivered(3);

        $newQuantity = $quantity->subtract(new DeliveryQuantity(5));

        self::assertEquals(0, $newQuantity->asFloat());
    }
}
