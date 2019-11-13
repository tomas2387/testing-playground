<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Line
{
    /** @var int */
    private $productId;

    /** @var string */
    private $description;

    /** @var float */
    private $quantity;

    /** @var int */
    private $quantityPrecision;

    /** @var float */
    private $tariff;

    /** @var Currency */
    private $currency;

    /** @var float|null */
    private $discount;

    /** @var Vat */
    private $vatCode;

    /** @var MoneyExchange */
    private $moneyExchange;

    public function __construct(
        int $productId,
        string $description,
        float $quantity,
        int $quantityPrecision,
        float $tariff,
        Currency $currency,
        ?float $discount,
        Vat $vatCode,
        MoneyExchange $moneyExchange
    )
    {
        $this->productId = $productId;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->quantityPrecision = $quantityPrecision;
        $this->tariff = $tariff;
        $this->currency = $currency;
        $this->discount = $discount;
        $this->vatCode = $vatCode;
        $this->moneyExchange = $moneyExchange;
    }

    public function amount(): float
    {
        return round(round($this->quantity, $this->quantityPrecision) * $this->tariff, 2);
    }

    public function discountAmount(): float
    {
        if ($this->discount === null) {
            return 0.0;
        }

        return round($this->amount() * $this->discount / 100, 2);
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
