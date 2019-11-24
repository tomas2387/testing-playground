<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Discount
{
    /** @var float|null */
    private $discount;

    private function __construct(?float $discount)
    {
        $this->discount = $discount;
    }

    public static function noDiscount(): self
    {
        return new self(null);
    }

    public static function fromFloat(float $discount): self
    {
        return new self($discount);
    }

    public function toFloat(): ?float
    {
        return $this->discount;
    }
}
