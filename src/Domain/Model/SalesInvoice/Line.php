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

    public function amount(): Amount
    {
        return $this->quantity->times($this->tariff);
    }

    public function netAmount(): Amount
    {
        return $this->amount()->applyDiscount($this->discount);
    }

    public function vatAmount(): float
    {
        $vatRate = $this->vatCode->rate();
        return $this->netAmount()->multiply($vatRate / 100)->toFloat();
    }
}
