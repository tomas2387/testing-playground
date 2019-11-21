<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Assert\Assertion;

final class Currency
{
    private const USD = 'USD';
    private const EUR = 'EUR';

    /** @var string */
    private $currency;

    private function __construct(string $currency)
    {
        Assertion::inArray($currency, [self::USD, self::EUR]);
        $this->currency = $currency;
    }

    public static function fromStringCurrency(string $currency): self
    {
        return new self($currency);
    }

    public static function USD()
    {
        return self::fromStringCurrency(self::USD);
    }

    public static function EUR()
    {
        return self::fromStringCurrency(self::EUR);
    }

    public function isEUR(): bool
    {
        return $this->currency === self::EUR;
    }

    public function isUSD(): bool
    {
        return $this->currency === self::USD;
    }

    public function is(Currency $otherCurrency): bool
    {
        return $this->currency === $otherCurrency->currency;
    }

    public function moneyExchangeTo(Currency $otherCurrency)
    {
        if ($this->isUSD() && $otherCurrency->isEUR()) {
            return MoneyExchange::fromUSDtoEUR();
        } elseif ($this->isEUR() && $otherCurrency->isUSD()) {
            return MoneyExchange::fromEURtoUSD();
        }
        return MoneyExchange::noChange();
    }
}
