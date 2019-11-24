<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

final class Product
{
    /** @var int */
    private $id;
    /** @var string */
    private $description;

    private function __construct(int $id, string $description)
    {
        $this->id = $id;
        $this->description = $description;
    }

    public static function fromIdAndDescription(int $id, string $description): self
    {
        return new self($id, $description);
    }
}
