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

    public function isEUR(): bool
    {
        return $this->currency === self::EUR;
    }
}
