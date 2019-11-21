<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Tariff
{
    /** @var float */
    private $tariff;

    private function __construct(float $tariff)
    {
        $this->tariff = $tariff;
    }

    public static function fromTariff(float $tariff): self
    {
        return new self($tariff);
    }

    public function toFloat(): float
    {
        return $this->tariff;
    }

}
