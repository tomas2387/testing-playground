<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Line
{
    /** @var Quantity */
    private $quantity;

    /** @var Tariff */
    private $tariff;

    /** @var Currency */
    private $currency;

    /** @var Discount */
    private $discount;

    /** @var Vat */
    private $vatCode;

    /** @var Product */
    private $product;

    public function __construct(
        Product $product,
        Quantity $quantity,
        Tariff $tariff,
        Currency $currency,
        Discount $discount,
        Vat $vatCode
    )
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->tariff = $tariff;
        $this->currency = $currency;
        $this->discount = $discount;
        $this->vatCode = $vatCode;
    }

    public function amount(): float
    {
        return round($this->quantity->times($this->tariff), 2);
    }

    public function discountAmount(): float
    {
        if ($this->discount->toFloat() === null) {
            return 0.0;
        }

        return round($this->amount() * $this->discount->toFloat() / 100, 2);
    }

    public function netAmount(): float
    {
        return round($this->amount() - $this->discountAmount(), 2);
    }

    public function vatAmount(): float
    {
        $vatRate = $this->vatCode->rate();
        return round($this->netAmount() * $vatRate / 100, 2);
    }
}
