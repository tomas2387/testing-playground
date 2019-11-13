<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Assert\Assertion;

final class State
{
    private const DRAFT = 'draft';
    private const FINALIZED = 'finalized';
    private const CANCELLED = 'cancelled';

    /** @var string */
    private $state;

    private function __construct(string $state)
    {
        Assertion::inArray($state, [self::CANCELLED, self::FINALIZED, self::DRAFT]);
        $this->state = $state;
    }

    public static function fromString(string $state): self
    {
        return new self($state);
    }

    public static function draft(): self
    {
        return new self(self::DRAFT);
    }

    public static function finalized(): self
    {
        return new self(self::FINALIZED);
    }

    public static function cancelled()
    {
        return new self(self::CANCELLED);
    }

    public function isFinalized(): bool
    {
        return $this->state === self::FINALIZED;
    }

    public function isCancelled(): bool
    {
        return $this->state === self::CANCELLED;
    }
}
