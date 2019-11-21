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

    /** @var Line[] */
    private $lines = [];

    /** @var DateTimeImmutable */
    private $invoiceDate;

    /** @var State */
    private $state;

    public function __construct(CustomerId $customerId, DateTimeImmutable $invoiceDate, Currency $currency)
    {
        $this->state = State::draft();
        $this->customerId = $customerId;
        $this->invoiceDate = $invoiceDate;
        $this->currency = $currency;
    }

    public function addLine(
        int $productId,
        string $description,
        Quantity $quantity,
        Tariff $tariff,
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
            $vatCode
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
        $moneyExchange = $this->currency->moneyExchangeTo(Currency::EUR());
        return round($this->totalNetAmount() / $moneyExchange->toFloat(), 2);
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
        $moneyExchange = $this->currency->moneyExchangeTo(Currency::EUR());
        return round($this->totalVatAmount() / $moneyExchange->toFloat(), 2);
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
