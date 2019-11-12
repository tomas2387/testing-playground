<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Assert\Assertion;

class Vat
{
    /** @var string */
    private $code;

    private function __construct(string $code)
    {
        Assertion::inArray($code, ['S', 'L']);
        $this->code = $code;
    }

    public static function fromVatCode(string $code): self
    {
        return new self($code);
    }

    public function asString(): string
    {
        return $this->code;
    }
}
