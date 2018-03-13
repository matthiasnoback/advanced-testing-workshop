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
}
