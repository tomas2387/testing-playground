<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use DateTimeImmutable;
use RuntimeException;

final class SalesInvoice
{
    /** @var CustomerId */
    private $customerId;

    /** @var Currency */
    private $currency;

    /** @var MoneyExchange */
    private $moneyExchange;

    /** @var Line[] */
    private $lines = [];

    /** @var DateTimeImmutable */
    private $invoiceDate;

    /** @var State */
    private $state;

    public function __construct()
    {
        $this->state = State::draft();
        $this->moneyExchange = MoneyExchange::noChange();
    }

    public function setCustomerId(CustomerId $customerId): void
    {
        $this->customerId = $customerId;
    }

    public function setInvoiceDate(DateTimeImmutable $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function setMoneyExchange(MoneyExchange $moneyExchange): void
    {
        $this->moneyExchange = $moneyExchange;
    }

    public function addLine(
        int $productId,
        string $description,
        Quantity $quantity,
        float $tariff,
        ?float $discount,
        Vat $vatCode
    ): void
    {
        $this->lines[] = new Line(
            $productId,
            $description,
            $quantity,
            $tariff,
            $this->currency,
            $discount,
            $vatCode,
            $this->moneyExchange
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
        if ($this->currency->is($this->moneyExchange->currencyToExchange())) {
            return $this->totalNetAmount();
        }

        return round($this->totalNetAmount() / $this->moneyExchange->toFloat(), 2);
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
        if ($this->currency->is($this->moneyExchange->currencyToExchange())) {
            return $this->totalVatAmount();
        }

        return round($this->totalVatAmount() / $this->moneyExchange->toFloat(), 2);
    }

    public function finalize(): void
    {
        if ($this->state->isCancelled()) {
            throw new RuntimeException('can\'t finalize. Invoice is already cancelled');
        }
        $this->state = State::finalized();
    }

    public function cancel(): void
    {
        if ($this->state->isFinalized()) {
            throw new RuntimeException('can\'t cancel. Invoice is already finalized');
        }
        $this->state = State::cancelled();
    }

    public function isFinalized(): bool
    {
        return $this->state->isFinalized();
    }

    public function isCancelled(): bool
    {
        return $this->state->isCancelled();
    }
}
