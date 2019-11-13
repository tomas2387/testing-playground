<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class MoneyExchange
{
    /** @var Currency */
    private $from;
    /** @var Currency */
    private $to;
    /** @var float */
    private $exchangeRate;

    public function __construct(float $exchangeRate, Currency $from, Currency $to)
    {
        $this->from = $from;
        $this->to = $to;
        $this->exchangeRate = $exchangeRate;
    }

    public static function fromUSDtoEUR(float $exchangeRate): self
    {
        return new self($exchangeRate, Currency::USD(), Currency::EUR());
    }

    public static function fromEURtoUSD(float $exchangeRate): self
    {
        return new self($exchangeRate, Currency::USD(), Currency::EUR());
    }

    public static function noChange()
    {
        return new self(1, Currency::EUR(), Currency::EUR());
    }

    public function currencyToExchange(): Currency
    {
        return $this->to;
    }

    public function toFloat(): float
    {
        return $this->exchangeRate;
    }
}
