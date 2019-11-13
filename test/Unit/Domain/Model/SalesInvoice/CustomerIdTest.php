<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CustomerIdTest extends TestCase
{
    /** @var Generator */
    private $faker;

    protected function setUp()
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    public function test_called_with_zero_or_invalid_should_throw_exception()
    {
        $id = $this->faker->numberBetween(-2500, 0);
        $this->expectException(InvalidArgumentException::class);
        CustomerId::fromInt($id);
    }

    public function test_called_with_integer_number_one_or_more_should_return()
    {
        $id = $this->faker->numberBetween(1, 2300);
        self::assertEquals($id, CustomerId::fromInt($id)->toInt());
    }
}
