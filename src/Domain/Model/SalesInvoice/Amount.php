<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Amount
{
    /** @var float */
    private $amount;

    private function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public static function fromFloat(float $amount): self
    {
        return new self($amount);
    }

    public function toFloat(): float
    {
        return round($this->amount, 2);
    }

    public function applyDiscount(Discount $discount): Amount
    {
        $discounted = round($this->amount * $discount->toFloat() / 100, 2);
        return Amount::fromFloat($this->amount - $discounted);
    }

    public function multiply(float $multiplicand): Amount
    {
        return Amount::fromFloat($this->amount * $multiplicand);
    }
}
