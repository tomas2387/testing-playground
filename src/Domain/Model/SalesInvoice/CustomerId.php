<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Assert\Assertion;

final class CustomerId
{
    /** @var int */
    private $id;

    private function __construct(int $id)
    {
        Assertion::greaterThan($id, 0, \sprintf("customer id %d is invalid", $id));
        $this->id = $id;
    }

    public static function fromInt(int $id): self
    {
        return new self($id);
    }

    public function toInt(): int
    {
        return $this->id;
    }
}
