<?php

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;

class PaymentService extends TestCase
{

    public function testCalculatePriceWithoutDiscount(): void
    {
        $productPrice = 10000;

        $taxNumber = $this->buildTaxNumber();

        $sut = new \App\Service\PaymentService();
        $sut->setTaxNumber('DE1234');

        $sut->setTaxNumberPatterns([$taxNumber]);

        $this->assertEquals(12400, $sut->calculateTax($productPrice));
    }

    public function testCalculatePriceWithDiscount(): void
    {
        $productPrice = 10000;

        $coupon = $this->buildCoupon();
        $taxNumber = $this->buildTaxNumber();

        $sut = new \App\Service\PaymentService();

        $sut->setTaxNumber('DE1234');
        $sut->setCouponCode('DIS6');
        $sut->setCopons([$coupon]);
        $sut->setTaxNumberPatterns([$taxNumber]);

        $this->assertEquals(11656, $sut->calculateTax($productPrice));
    }

    private function buildTaxNumber(): \App\Entity\TaxNumber {
        $taxNumber = new \App\Entity\TaxNumber;
        $taxNumber->setTax(24);
        $taxNumber->setPattern('DEXXXX');
        
        return $taxNumber;
    }

    private function buildCoupon(): \App\Entity\Coupon {
        $coupon = new \App\Entity\Coupon;
        $coupon->setName('DIS6');
        
        return $coupon;
    }
}
