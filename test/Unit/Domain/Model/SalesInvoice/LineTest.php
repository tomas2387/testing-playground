<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LineTest extends TestCase
{
    /** @var Generator */
    private $faker;

    protected function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function test_netAmount_called_with_no_discount_should_return_the_same_amount()
    {
        $line = new Line(
            $this->aProduct(),
            Quantity::fromQuantityAndPrecision(
                4.6533,
                2
            ),
            Tariff::fromTariff(
                10
            ),
            $this->aCurrency(),
            Discount::noDiscount(),
            Vat::lower()
        );

        self::assertEquals(46.5, $line->netAmount()->toFloat());
    }

    public function test_netAmount_called_with_50_percent_discount_should_return_half_amount()
    {
        $line = new Line(
            $this->aProduct(),
            Quantity::fromQuantityAndPrecision(
                4.6533,
                2
            ),
            Tariff::fromTariff(
                10
            ),
            $this->aCurrency(),
            Discount::fromFloat(50.0),
            Vat::lower()
        );

        self::assertEquals(23.25, $line->netAmount()->toFloat());
    }

    private function aProduct(): Product
    {
        return Product::fromIdAndDescription(
            $this->faker->numberBetween(0, 1000),
            $this->faker->sentence
        );
    }

    private function aCurrency(): Currency
    {
        return Currency::EUR();
    }
}
