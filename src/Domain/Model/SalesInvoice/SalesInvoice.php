<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use DateTimeImmutable;

final class SalesInvoice
{
    /** @var int */
    private $customerId;

    /** @var string */
    private $currency;

    /** @var float|null */
    private $exchangeRate;

    /** @var int */
    private $quantityPrecision;

    /** @var Line[] */
    private $lines = [];

    /** @var DateTimeImmutable */
    private $invoiceDate;

    /** @var State */
    private $state;

    public function __construct()
    {
        $this->state = State::draft();
    }

    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function setInvoiceDate(DateTimeImmutable $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function setExchangeRate(?float $exchangeRate): void
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function setQuantityPrecision(int $quantityPrecision): void
    {
        $this->quantityPrecision = $quantityPrecision;
    }

    public function addLine(
        int $productId,
        string $description,
        float $quantity,
        float $tariff,
        ?float $discount,
        Vat $vatCode
    ): void
    {
        $this->lines[] = new Line(
            $productId,
            $description,
            $quantity,
            $this->quantityPrecision,
            $tariff,
            $this->currency,
            $discount,
            $vatCode,
            $this->exchangeRate
        );
    }

    public function totalNetAmount(): float
    {
        $sum = 0.0;

        foreach ($this->lines as $line) {
            $sum += $line->netAmount();
        }

        return round($sum, 2);
    }

    public function totalNetAmountInLedgerCurrency(): float
    {
        if ($this->currency === 'EUR' || $this->exchangeRate == null) {
            return $this->totalNetAmount();
        }

        return round($this->totalNetAmount() / $this->exchangeRate, 2);
    }

    public function totalVatAmount(): float
    {
        $sum = 0.0;

        foreach ($this->lines as $line) {
            $sum += $line->vatAmount();
        }

        return round($sum, 2);
    }

    public function totalVatAmountInLedgerCurrency(): float
    {
        if ($this->currency === 'EUR' || $this->exchangeRate == null) {
            return $this->totalVatAmount();
        }

        return round($this->totalVatAmount() / $this->exchangeRate, 2);
    }

    public function finalize(): void
    {
        $this->state = State::finalized();
    }

    public function isFinalized(): bool
    {
        return $this->state->isFinalized();
    }

    public function cancel(): void
    {
        $this->state = State::cancelled();
    }

    public function isCancelled(): bool
    {
        return $this->state->isCancelled();
    }
}
