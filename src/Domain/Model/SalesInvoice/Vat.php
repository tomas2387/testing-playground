<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Assert\Assertion;
use DateTime;
use InvalidArgumentException;

final class Vat
{
    private const STANDARD_CODE = 'S';
    private const LOWER_CODE = 'L';

    private const STANDARD_RATE = 21.0;
    private const PRIOR_2019_RATE = 6.0;
    private const LOWER_RATE = 9.0;

    /** @var string */
    private $code;

    private function __construct(string $code)
    {
        Assertion::inArray($code, [self::STANDARD_CODE, self::LOWER_CODE]);
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

    public function rate(): float
    {
        if ($this->asString() === self::STANDARD_CODE) {
            return self::STANDARD_RATE;
        } elseif ($this->asString() === self::LOWER_CODE) {
            if (new DateTime('now') < DateTime::createFromFormat('Y-m-d', '2019-01-01')) {
                return self::PRIOR_2019_RATE;
            }
            return self::LOWER_RATE;
        }

        throw new InvalidArgumentException('Should not happen');
    }
}
