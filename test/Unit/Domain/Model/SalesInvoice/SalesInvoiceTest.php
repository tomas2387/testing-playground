<?php
declare(strict_types=1);

namespace Domain\Model\SalesInvoice;

use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class SalesInvoiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_calculates_the_correct_totals_for_an_invoice_in_foreign_currency(): void
    {
        $salesInvoice = new SalesInvoice(
            CustomerId::fromInt(1001),
            new DateTimeImmutable(),
            Currency::fromStringCurrency('USD')
        );

        $salesInvoice->addLine(
            Product::fromIdAndDescription(1,
                'Product with a 10% discount and standard VAT applied'),
            Quantity::fromQuantityAndPrecision(2.0, 3),
            Tariff::fromTariff(15.0),
            Discount::fromFloat(10.0),
            Vat::fromVatCode('S')
        );
        $salesInvoice->addLine(
            Product::fromIdAndDescription(2,
                'Product with no discount and low VAT applied'),
            Quantity::fromQuantityAndPrecision(3.123456, 3),
            Tariff::fromTariff(12.50),
            Discount::noDiscount(),
            Vat::fromVatCode('L')
        );

        /*
         * 2 * 15.00 - 10% = 27.00
         * +
         * 3.123 * 12.50 - 0% = 39.04
         * =
         * 66.04
         */
        self::assertEquals(66.04, $salesInvoice->totalNetAmount());

        /*
         * 66.04 / 1.3 = 50.80
         */
        self::assertEquals(50.80, $salesInvoice->totalNetAmountInLedgerCurrency());

        /*
         * 27.00 * 21% = 5.67
         * +
         * 39.04 * 9% = 3.51
         * =
         * 9.18
         */
        self::assertEquals(9.18, $salesInvoice->totalVatAmount());

        /*
         * 9.18 / 1.3 = 7.06
         */
        self::assertEquals(7.06, $salesInvoice->totalVatAmountInLedgerCurrency());
    }

    /**
     * @test
     */
    public function it_calculates_the_correct_totals_for_an_invoice_in_ledger_currency(): void
    {
        $salesInvoice = $this->createSalesInvoice();
        $salesInvoice->addLine(
            Product::fromIdAndDescription($this->aProductId(),
                'Product with a 10% discount and standard VAT applied'),
            Quantity::fromQuantityAndPrecision(2.0, 3),
            Tariff::fromTariff(15.0),
            Discount::fromFloat(10.0),
            Vat::fromVatCode('S')
        );
        $salesInvoice->addLine(
            Product::fromIdAndDescription($this->anotherProductId(),
                'Product with no discount and low VAT applied'),
            Quantity::fromQuantityAndPrecision(3.123456, 3),
            Tariff::fromTariff(12.50),
            Discount::noDiscount(),
            Vat::fromVatCode('L')
        );

        self::assertEquals($salesInvoice->totalNetAmount(), $salesInvoice->totalNetAmountInLedgerCurrency());
        self::assertEquals($salesInvoice->totalVatAmount(), $salesInvoice->totalVatAmountInLedgerCurrency());
    }

    /**
     * @test
     */
    public function it_fails_when_you_provide_an_unknown_vat_code(): void
    {
        $salesInvoice = $this->createSalesInvoice();

        $this->expectException(InvalidArgumentException::class);

        $salesInvoice->addLine(
            Product::fromIdAndDescription($this->aProductId(),
                $this->aDescription()),
            $this->aQuantity(),
            $this->aTariff(),
            Discount::noDiscount(),
            Vat::fromVatCode('Invalid VAT code')
        );
    }

    /**
     * @test
     */
    public function you_can_finalize_an_invoice(): void
    {
        $salesInvoice = $this->createSalesInvoice();
        self::assertFalse($salesInvoice->isFinalized());

        $salesInvoice->finalize();

        self::assertTrue($salesInvoice->isFinalized());
    }

    /**
     * @test
     */
    public function you_can_cancel_an_invoice(): void
    {
        $salesInvoice = $this->createSalesInvoice();
        self::assertFalse($salesInvoice->isCancelled());

        $salesInvoice->cancel();

        self::assertTrue($salesInvoice->isCancelled());
    }

    public function test_you_cant_cancel_a_finalized_invoice()
    {
        $salesInvoice = $this->createSalesInvoice();
        $salesInvoice->finalize();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/already finalized/');
        $salesInvoice->cancel();
    }

    public function test_you_cant_finalize_a_cancelled_invoice()
    {
        $salesInvoice = $this->createSalesInvoice();
        $salesInvoice->cancel();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageRegExp('/already cancelled/');
        $salesInvoice->finalize();
    }

    private function createSalesInvoice(): SalesInvoice
    {
        return new SalesInvoice(
            CustomerId::fromInt(1001),
            new DateTimeImmutable(),
            Currency::fromStringCurrency('EUR')
        );
    }

    private function aDescription(): string
    {
        return 'Description';
    }

    private function aQuantity(): Quantity
    {
        return Quantity::fromQuantityAndPrecision(2.0, 3);
    }

    private function aTariff(): Tariff
    {
        return Tariff::fromTariff(15.0);
    }

    private function aProductId(): int
    {
        return 1;
    }

    private function anotherProductId(): int
    {
        return 2;
    }
}
