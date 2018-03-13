<?php
declare(strict_types=1);

namespace SalesOrder;

final class QuantityDelivered
{
    /**
     * @var float
     */
    private $quantity;

    public function __construct(float $quantity)
    {
        if ($quantity < 0) {
            throw new \InvalidArgumentException('Quantity should not be smaller than 0');
        }

        $this->quantity = $quantity;
    }

    public function asFloat(): float
    {
        return $this->quantity;
    }

    public function add(DeliveryQuantity $deliveryQuantity): QuantityDelivered
    {
        return new self(
            $this->quantity + $deliveryQuantity->asFloat()
        );
    }

    public function subtract(DeliveryQuantity $deliveryQuantity): QuantityDelivered
    {
        $newQuantity = $this->quantity - $deliveryQuantity->asFloat();

        if ($newQuantity < 0) {
            $newQuantity = 0;
        }

        return new self($newQuantity);
    }
}
