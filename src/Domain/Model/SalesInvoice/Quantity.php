<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use function round;

final class Quantity
{
    /** @var float */
    private $quantity;
    /** @var int */
    private $quantityPrecision;

    private function __construct(float $quantity, int $quantityPrecision)
    {
        $this->quantity = $quantity;
        $this->quantityPrecision = $quantityPrecision;
    }

    public static function fromQuantityAndPrecision(float $quantity, int $quantityPrecision): self
    {
        return new self($quantity, $quantityPrecision);
    }

    public function multiply(float $tariff): float
    {
        return $this->toFloat() * $tariff;
    }

    public function toFloat(): float
    {
        return round($this->quantity, $this->quantityPrecision);
    }

    public function times(Tariff $tariff): float
    {
        return $this->multiply($tariff->toFloat());
    }
}
