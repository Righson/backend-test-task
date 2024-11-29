<?php

namespace App\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;

class TaxServiceTest extends TestCase
{

    public function testCalculatePriceWithoutDiscount(): void
    {
        $productPrice = 10000;
        $sut = new \App\Service\TaxService();
        $this->assertEquals(12400, $sut->calculateTax($productPrice, 24));
    }

    public function testCalculatePriceWithDiscount(): void
    {
        $productPrice = 10000;
        $sut = new \App\Service\TaxService();
        $this->assertEquals(11656, $sut->calculateTax($productPrice, 24, 6));
    }

}
